<?php defined('BILLINGMASTER') or die;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Models\LeadModel;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TrackingDataCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TrackingDataCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TrackingDataCustomFieldValueModel;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Collections\CustomFieldGroupsCollection;
use AmoCRM\Models\TagModel;

class AmoCRMApi {
    const TOKEN_FILE = __DIR__ . '/../tmp/token_info.json';

    private $integr_id;
    private $secret_key;
    private $prtn_id_fname;
    private $prtns_payouts_fname;
    private $order_id_fname;
    private $instlmnt_next_pay_date_fname;
    private $redirect_url;
    private $api_client;
    private $access_token = null;
    private $ym_counter;
    
    public function __construct($main_settings, $params) {
        $this->integr_id = trim($params['params']['integr_id']);
        $this->secret_key = trim($params['params']['secret_key']);
        $this->prtn_id_fname = isset($params['params']['partners_ids_fname']) ? trim($params['params']['partners_ids_fname']) : null;
        $this->prtns_payouts_fname = isset($params['params']['partners_payouts_fname']) ? trim($params['params']['partners_payouts_fname']) : null;
        $this->order_id_fname = isset($params['params']['order_id_fname']) ? trim($params['params']['order_id_fname']) : null;
        $this->instlmnt_next_pay_date_fname = isset($params['params']['instlmnt_next_pay_date_fname']) ? trim($params['params']['instlmnt_next_pay_date_fname']) : null;
        $this->redirect_url = "{$main_settings['script_url']}/admin/amocrmsetting/oauth";
        $this->api_client = new AmoCRMApiClient($this->integr_id, $this->secret_key, $this->redirect_url);
        $this->ym_counter = $main_settings['yacounter'];
    }
    
    public function getApiClient() {
        return $this->api_client;
    }
    
    public function auth() {
        if ($this->access_token = $this->getToken()) {
            $this->api_client
                ->setAccessToken($this->access_token)
                ->setAccountBaseDomain($this->access_token->getValues()['baseDomain'])
                ->onAccessTokenRefresh(function (AccessTokenInterface $access_token, $base_domain) {
                    $data = array(
                        'accessToken' => $access_token->getToken(),
                        'refreshToken' => $access_token->getRefreshToken(),
                        'expires' => $access_token->getExpires(),
                        'baseDomain' => $base_domain,
                    );
                    $this->saveToken($data);
                });
            
            return true;
        }

        return false;
    }
    
    public function getToken() {
        if (!file_exists(self::TOKEN_FILE)) {
            return false;
        }
        
        if ($this->access_token !== NULL) {
            return $this->access_token;
        } else {
            $access_token = json_decode(file_get_contents(self::TOKEN_FILE), true);
    
            if (isset($access_token) && isset($access_token['accessToken']) && isset($access_token['refreshToken'])
                && isset($access_token['expires']) && isset($access_token['baseDomain'])) {
                $data = array(
                    'access_token' => $access_token['accessToken'],
                    'refresh_token' => $access_token['refreshToken'],
                    'expires' => $access_token['expires'],
                    'baseDomain' => $access_token['baseDomain'],
                );
                
                return new AccessToken($data);
            } else {
                return false;
            }
        }
    }


    /**
     * @param $id
     * @param null $email
     * @return ContactsCollection|bool|null
     */
    public function searchContactsCollection($id, $email = null) {
        try {
            $contacts = $this->api_client->contacts();
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }
    
        $filter = new ContactsFilter();
        $search = $id ? $filter->setIds([$id]) : $filter->setQuery($email);

        try {
            $contacts_collection = $contacts->get($search);
        } catch (AmoCRMApiException $e) {
            return false;
        }
    
        return isset($contacts_collection) && is_object($contacts_collection) ? $contacts_collection : false;
    }

    public function searchLead($lead_id) {
        try {
            $lead = $this->api_client->leads()->getOne($lead_id);
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }

        return $lead;
    }

    public function updateLeadsCollection($lead_model, $lead_name, $pip_id, $stage_id, $amount = null, $partner_id = null,
        $partners_payouts = null, $prod_names = [])
    {
        if ($lead_name) {
            $lead_model->setName($lead_name);
        }

        $lead_model
            ->setPipelineId((int)$pip_id)
            ->setStatusId((int)$stage_id);

        if ($amount !== null) {
            $lead_model->setPrice($amount);
        }

        if ($prod_names) {
            $prod_names = array_unique($prod_names);
            $tags_collection = $this->createTagsCollection($prod_names);

            if ($tags_collection) {
                $lead_model->setTags($tags_collection);
            }
        }

        if ($partner_id && $this->prtn_id_fname) { // партнерские данные
            $customFieldsService = $this->api_client->customFields(EntityTypesInterface::LEADS);
        
            try {
                $custom_fields = $customFieldsService->get();
                $field = $custom_fields->getBy('name', $this->prtn_id_fname);
            
                if ($field) {
                    $fields_collection = new CustomFieldsValuesCollection();
                    $this->addCustomFields($custom_fields, $fields_collection, $field, $partner_id);
                    
                    if ($partners_payouts && $this->prtns_payouts_fname) {
                        $field = $custom_fields->getBy('name', $this->prtns_payouts_fname);
                        if ($field) {
                            $this->addCustomFields($custom_fields, $fields_collection, $field, $partners_payouts);
                        }
                    }

                    $lead_model->setCustomFieldsValues($fields_collection);
                }
            } catch (AmoCRMApiException $e) {
                $this->writeError($e);
                return false;
            }
        }
        
        $leads_collection = new LeadsCollection();
        $leads_collection->add($lead_model);

        try {
            $this->api_client->leads()->update($leads_collection);
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }

        return true;
    }


    /**
     * @param $name
     * @param $cl_surname
     * @param $email
     * @param null $phone
     * @return ContactsCollection
     */
    public function createContactsCollection($name, $cl_surname, $email, $phone = null) {
        $fields_collection = new CustomFieldsValuesCollection();

        $email_field = new MultitextCustomFieldValuesModel();// ДОБАВЛЯЕМ В КОНТАКТ ЕМЭЙЛ
        $email_field->setFieldCode('EMAIL');
        $email_field->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add((new MultitextCustomFieldValueModel())
                    ->setEnum('WORK')
                    ->setValue($email))
        );
        $fields_collection->add($email_field);

        if ($phone) {
            $phone_field = new MultitextCustomFieldValuesModel();// ДОБАВЛЯЕМ В КОНТАКТ ТЕЛЕФОН
            $phone_field->setFieldCode('PHONE');
            $phone_field->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add((new MultitextCustomFieldValueModel())
                        ->setEnum('WORKDD')
                        ->setValue($phone))
            );
            $fields_collection->add($phone_field);
        }

        $contact = new ContactModel();
        $contact->setName($name);
        if ($cl_surname) {
            $contact->setLastName($cl_surname);
        }
        $contact->setCustomFieldsValues($fields_collection);

        $contacts_collection = new ContactsCollection();
        $contacts_collection->add($contact);
        
        return $contacts_collection;
    }

    public function addContactsCollection($contacts_collection) {
        try {
            $contacts_collection = $this->api_client->contacts()->add($contacts_collection);
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }
    
        return $contacts_collection;
    }
    
    public function createLeadsCollection($lead_name, $pip_id, $stage_id, $sum = null, $prod_names = null, $order_date = null,
        $statistics_data = null, $partners_data = null, $instlmnt_next_pay_date = null)
    {
        $lead = new LeadModel();
        
        $lead->setName($lead_name)
            ->setPipelineId((int)$pip_id)
            ->setStatusId((int)$stage_id)
            ->setResponsibleUserId(0)
            ->setCreatedAt(time());

        if ($sum) {
            $lead->setPrice($sum);
        }

        if ($prod_names) {
            $prod_names = array_unique($prod_names);
            $tags_collection = $this->createTagsCollection($prod_names);
            
            if ($tags_collection) {
                $lead->setTags($tags_collection);
            }
        }

        if (($this->order_id_fname && $order_date) || $statistics_data) { // дополнительные поля
            $customFieldsService = $this->api_client->customFields(EntityTypesInterface::LEADS);
            $fields_collection = new CustomFieldsValuesCollection();

            try {
                $custom_fields = $customFieldsService->get();

                if ($this->order_id_fname) {
                    $field = $custom_fields->getBy('name', $this->order_id_fname);

                    if ($field) {
                        $this->addCustomFields($custom_fields, $fields_collection, $field, $order_date);
                    }
                }

                if ($statistics_data) {
                    if (isset($statistics_data['userId_YM']) && $statistics_data['userId_YM']) {
                        $this->addTrackingDataFields($custom_fields, $fields_collection, '_ym_uid', $statistics_data['userId_YM']);
                    }

                    if ($this->ym_counter) {
                        $this->addTrackingDataFields($custom_fields, $fields_collection, '_ym_counter', $this->ym_counter);
                    }

                    if (isset($statistics_data['userId_GA']) && $statistics_data['userId_GA']) {
                        $this->addTrackingDataFields($custom_fields, $fields_collection, 'gclientid', $statistics_data['userId_GA']);
                    }

                    if (isset($statistics_data['roistat_visitor']) && $statistics_data['roistat_visitor']) {
                        $this->addTrackingDataFields($custom_fields, $fields_collection, 'roistat', $statistics_data['roistat_visitor']);
                    }

                    if (isset($statistics_data['userId_FB']) && $statistics_data['userId_FB']) {
                        $this->addTrackingDataFields($custom_fields, $fields_collection, 'fbclid', $statistics_data['userId_FB']);
                    }

                    if (isset($statistics_data['utm']) && $statistics_data['utm']) {
                        foreach ($statistics_data['utm'] as $key => $value) {
                            $this->addTrackingDataFields($custom_fields, $fields_collection, $key, $value);
                        }
                    }
                }

                if ($partners_data && $this->prtn_id_fname) { // партнерские данные
                    $field = $custom_fields->getBy('name', $this->prtn_id_fname);
                    if ($field) {
                        $this->addCustomFields($custom_fields, $fields_collection, $field, $partners_data['partner_id']);

                        if ($partners_data['partners_payouts'] && $this->prtns_payouts_fname) {
                            $field = $custom_fields->getBy('name', $this->prtns_payouts_fname);
                            if ($field) {
                                $this->addCustomFields($custom_fields, $fields_collection, $field, $partners_data['partners_payouts']);
                            }
                        }
                    }
                }

                if ($this->instlmnt_next_pay_date_fname && $instlmnt_next_pay_date) {
                    $field = $custom_fields->getBy('name', $this->instlmnt_next_pay_date_fname);

                    if ($field) {
                        $this->addCustomFields($custom_fields, $fields_collection, $field, $instlmnt_next_pay_date, 'date');
                    }
                }

                $lead->setCustomFieldsValues($fields_collection);
            } catch (AmoCRMApiException $e) {
                $this->writeError($e);
                return false;
            }
        }

        $leads_collection = new LeadsCollection();
        $leads_collection->add($lead);

        return $leads_collection;
    }

    private function createTagsCollection($prod_names) {
        $tagsCollection = new TagsCollection();
        
        foreach ($prod_names as $prod_name) {
            $prod_name = trim($prod_name);
            
            if (!$prod_name) {
                continue;
            }
            
            $tag = new TagModel();
            $tag->setName($prod_name);
            $tagsCollection->add($tag);
        }
        
        return $tagsCollection;
    }


    /**
     * ДОБАВИТЬ ДАННЫЕ В КОЛЛЕКЦИЮ
     * @param $custom_fields
     * @param $fields_collection
     * @param $field
     * @param $value
     * @param string $type
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     */
    private function addCustomFields(&$custom_fields, &$fields_collection, $field, $value, $type = 'text')
    {
        if ($type == 'date') {
            $valueModel = (new DateCustomFieldValueCollection())
                ->add((new DateCustomFieldValueModel())->setValue((int)$value));
        } else {
            $valueModel = (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue((string)$value));
        }

        $fields_collection->add(
            (new TextCustomFieldValuesModel())
                ->setFieldId($field->getId())
                ->setValues($valueModel)
        );
    }


    /**
     * ДОБАВИТЬ ДАННЫЕ В КОЛЛЕКЦИЮ
     * @param $custom_fields
     * @param $fields_collection
     * @param $field
     * @param $value
     */
    private function addCustomDateFields(&$custom_fields, &$fields_collection, $field, $value)
    {
        $fields_collection->add(
            (new TextCustomFieldValuesModel())
                ->setFieldId($field->getId())
                ->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue((string)$value))
                )
        );
    }


    /**
     * ДОБАВИТЬ СТАТИСТИЧЕСКИЕ ДАННЫЕ В КОЛЛЕКЦИЮ
     * @param $custom_fields
     * @param $fields_collection
     * @param $name
     * @param $value
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     */
    public function addTrackingDataFields(&$custom_fields, &$fields_collection, $name, $value) {
        $field = $custom_fields->getBy('name', $name);
        if ($field) {
            if ($field->groupId == 'statistic' && !$field->isDeletable) {
                $fields_collection->add(
                    (new TrackingDataCustomFieldValuesModel())
                        ->setFieldId($field->getId())
                        ->setValues(
                            (new TrackingDataCustomFieldValueCollection())
                                ->add((new TrackingDataCustomFieldValueModel())->setValue((string)$value))
                        )
                );
            } else {
                $this->addCustomFields($custom_fields, $fields_collection, $field, $value);
            }
        }
    }


    public function addLeadsCollection($leads_collection) {
        try {
            $leads_collection = $this->api_client->leads()->add($leads_collection);
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }
        
        return $leads_collection;
    }
    
    public function addContact2Lead($contacts_collection, $leads_collection) {
        $contact = $contacts_collection->first();
    
        $links_collection = new LinksCollection();
        $links_collection->add($contact);
        
        $lead = $leads_collection->first();
    
        try {
            $linksCollection = $this->api_client->leads()->link($lead, $links_collection);
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            return false;
        }
        
        return $linksCollection;
    }
    
    public function getPipelines() {
        try {
            $pipelines_service = $this->api_client->pipelines();
            $pipelines = $pipelines_service->get();
            
            return $pipelines;
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            
            return false;
        }
    }
    
    public function getStatuses($pipeline_id) {
        try {
            $statuses_service = $this->api_client->statuses(intval($pipeline_id));
            $statuses = $statuses_service->get();
            
            return $statuses;
        } catch (AmoCRMApiException $e) {
            $this->writeError($e);
            
            return false;
        }
    }

    public function saveToken($access_token) {
        if (isset($access_token) && isset($access_token['accessToken']) && isset($access_token['refreshToken'])
            && isset($access_token['expires']) && isset($access_token['baseDomain'])) {
            $data = array(
                'accessToken' => $access_token['accessToken'],
                'expires' => $access_token['expires'],
                'refreshToken' => $access_token['refreshToken'],
                'baseDomain' => $access_token['baseDomain'],
            );
            file_put_contents(self::TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($access_token, true));
        }
    }

    public function writeError(AmoCRMApiException $e) {
        $errorTitle = $e->getTitle();
        $code = $e->getCode();
        $error = date('d.m.Y H:i:s', time()) . " Error: $errorTitle Code: $code";
        file_put_contents(__DIR__ . '/../log.txt', PHP_EOL . $error, FILE_APPEND);
    }
}
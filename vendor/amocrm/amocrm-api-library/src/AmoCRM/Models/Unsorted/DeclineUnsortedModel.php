<?php

namespace AmoCRM\Models\Unsorted;

use AmoCRM\Client\AmoCRMApiRequest;
use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use Illuminate\Contracts\Support\Arrayable;

class DeclineUnsortedModel implements Arrayable
{
    /**
     * @var string
     */
    protected $uid;

    /**
     * @var int
     */
    protected $pipelineId;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @var LeadsCollection|null
     */
    protected $leads;

    /**
     * @var ContactsCollection|null
     */
    protected $contacts;

    /**
     * @var CompaniesCollection|null
     */
    protected $companies;

    /**
     * @param array $result
     * @return self
     */
    public static function fromArray(array $result): self
    {
        $model = new self();

        $model->setUid($result['uid'])
            ->setCategory($result['category'])
            ->setPipelineId($result['pipeline_id'])
            ->setCreatedAt($result['created_at']);

        if (!empty($result[AmoCRMApiRequest::EMBEDDED]['leads'])) {
            $leadsCollection = new LeadsCollection();
            $leadsCollection = $leadsCollection->fromArray($result[AmoCRMApiRequest::EMBEDDED]['leads']);
            $model->setLeads($leadsCollection);
        }

        if (!empty($result[AmoCRMApiRequest::EMBEDDED]['contacts'])) {
            $contactsCollection = new ContactsCollection();
            $contactsCollection = $contactsCollection->fromArray($result[AmoCRMApiRequest::EMBEDDED]['contacts']);
            $model->setContacts($contactsCollection);
        }

        if (!empty($result[AmoCRMApiRequest::EMBEDDED]['companies'])) {
            $companiesCollection = new CompaniesCollection();
            $companiesCollection = $companiesCollection->fromArray($result[AmoCRMApiRequest::EMBEDDED]['companies']);
            $model->setCompanies($companiesCollection);
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'uid' => $this->getUid(),
            'category' => $this->getCategory(),
            'pipeline_id' => $this->getPipelineId(),
            'created_at' => $this->getCreatedAt(),
            'leads' => $this->getLeads()->toArray(),
            'contacts' => $this->getContacts()->toArray(),
            'companies' => $this->getCompanies()->toArray(),
        ];
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return DeclineUnsortedModel
     */
    public function setUid(string $uid): DeclineUnsortedModel
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return int
     */
    public function getPipelineId(): int
    {
        return $this->pipelineId;
    }

    /**
     * @param int $pipelineId
     * @return DeclineUnsortedModel
     */
    public function setPipelineId(int $pipelineId): DeclineUnsortedModel
    {
        $this->pipelineId = $pipelineId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return DeclineUnsortedModel
     */
    public function setCategory(string $category): DeclineUnsortedModel
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return DeclineUnsortedModel
     */
    public function setCreatedAt(int $createdAt): DeclineUnsortedModel
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return LeadsCollection|null
     */
    public function getLeads(): ?LeadsCollection
    {
        return $this->leads;
    }

    /**
     * @param LeadsCollection|null $leads
     * @return DeclineUnsortedModel
     */
    public function setLeads(?LeadsCollection $leads): DeclineUnsortedModel
    {
        $this->leads = $leads;

        return $this;
    }

    /**
     * @return ContactsCollection|null
     */
    public function getContacts(): ?ContactsCollection
    {
        return $this->contacts;
    }

    /**
     * @param ContactsCollection|null $contacts
     * @return DeclineUnsortedModel
     */
    public function setContacts(?ContactsCollection $contacts): DeclineUnsortedModel
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @return CompaniesCollection|null
     */
    public function getCompanies(): ?CompaniesCollection
    {
        return $this->companies;
    }

    /**
     * @param CompaniesCollection|null $companies
     * @return DeclineUnsortedModel
     */
    public function setCompanies(?CompaniesCollection $companies): DeclineUnsortedModel
    {
        $this->companies = $companies;

        return $this;
    }
}

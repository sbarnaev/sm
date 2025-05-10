<?php defined('BILLINGMASTER') or die;

class Order {
    
    
    public static function renderOrder($order, $payment_id = null, $subscription_id = null)
    {
        $date = time();
        $setting = System::getSetting();
        $send_pass = $setting['enable_cabinet'];
        
        // АВТО ДОБАВЛЕНИЕ ПРОДУКТОВ К ЗАКАЗУ
        // Получить список продуктов заказа 
        $items = self::getOrderItems($order['order_id']);
        foreach($items as $item) {
            $product = Product::getProductDataForSendOrder($item['product_id']); // Получить данные продукта

            if (!empty($product['auto_add'])) {
                $auto_add = unserialize(base64_decode($product['auto_add']));
                foreach($auto_add as $product_add_id) {
                    $product_add_data = Product::getMinProductById($product_add_id);

                    if ($product_add_data) {
                        $add = self::UpdateOrderAfterUpsell($order['order_date'], $product_add_id, 0, 1, $product_add_data['product_name']);
                    }
                }
            }
            
            if ($product['send_pass'] < 2) {
                $send_pass = 0;
            }
        }
        
        $items = self::getOrderItems($order['order_id']);

        // ОБНОВИТЬ ЗАКАЗ В БД и записать время оплаты
        $upd = self::UpdateOrderStatus($order['order_date'], $date, $payment_id);


        // ОБРАБОТКА
        if ($upd) {
            
            $extension = System::CheckExtensension('partnership', 1);
            if ($extension) $aff_params = unserialize(System::getExtensionSetting('partnership')); // Получить настройки партнёрки
            
            if (isset($_SESSION['cart'])) unset($_SESSION['cart']);

            $total_aff = 0;
            
            // Подсчитать стоимость продуктов заказа
            $total_summ = self::getOrderTotalSum($order['order_id']);

            // СОЗДАТЬ КЛИЕНТА
            $role = 'user';
            if ($total_summ > 0) {
                $is_client = 1;
                $enter_method = 'paid';
            } else {
                $is_client = 0;
                $enter_method = 'free';
            }

            // Создаём пароль клиенту
            $surname = null;
            $nick_telegram = null;
            $nick_instagram = null;

            if ($order['order_info'] != null) {
                $order_info = unserialize(base64_decode($order['order_info']));
                if (isset($order_info['surname'])) $surname = $order_info['surname'];
                if (isset($order_info['nick_telegram'])) $nick_telegram = $order_info['nick_telegram'];
                if (isset($order_info['nick_instagram'])) $nick_instagram = $order_info['nick_instagram'];
            }

            $client = $client_data = User::getUserDataByEmail($order['client_email'], null); // получаем данные клиента, если он есть.
            if (!$client) {
                $client = User::AddNewClient($order['client_name'], $order['client_email'], $order['client_phone'], $order['client_city'],
                    $order['client_address'], $order['client_index'], $role, $is_client, $date, $enter_method, $order['visit_param'],
                    1, null, null, $send_pass, $setting['register_letter'], 0, null, $order['partner_id'],
                    $surname, $nick_telegram, $nick_instagram
                );
                sleep(2); //TODO SM-1351 это не очень правильно...
            } elseif ($client['is_client'] == 0 && $is_client == 1) {
                $result = User::updateClientStatus($client['user_id'], $is_client);
            }

            // Проверка канала у клиента
            if ($client['channel_id'] != 0 && $client['channel_id'] != $order['channel_id']) {
                // Обновление channel_id у заказа
                self::updateChannel_id($order['order_id'], $client['channel_id']);
            }


			// ЕСЛИ РАССРОЧКА, ТО ОБНОВИТЬ ЗАПИСЬ В КАРТЕ РАССРОЧЕК
            $installment_data = $installment_map = $installment_map_data = $next = false;
			if ($order['installment_map_id'] != 0) {
				$installment_map_data = self::getInstallmentMapData($order['installment_map_id']); // получить данные договора
				if ($installment_map_data) {
					$installment_map = $installment_map_data;
					$installment_data = Product::getInstallmentData($installment_map_data['installment_id']); // получить настройки рассрочки

                    if ($installment_map_data['pay_actions'] != null) {
                        $next = 1;
                    }
                    
					// проверка на досрочное погашение
                    $ahead = $installment_map_data['ahead_id'] == $order['order_id'] ? 1 : false;
                    $installment_map_data = Installment::updateInstallMap($order, $installment_data, // обновить данные в карте рассрочек
                        $installment_map_data, $ahead, $total_summ
                    );
				}
			}


            /**
             * ЦИКЛ --- Перебор продуктов в заказе -----------
             */
            $order_items = null;
            $rand_str = System::generateStr(9);
            $partners_payouts = 0;
            $partner_id = null;
            $partner2_id = $partner3_id = false;
            $prod_names = array_column($items, 'product_name');
            $prod_srv_names = [];
            $prod_ids = [];
            $sendCheckBL = false;

            foreach($items as $item) {
                // Получить данные продукта
                $product = Product::getProductDataForSendOrder($item['product_id']);
                $prod_srv_names[] = $product['service_name'];
                $prod_ids[] = $item['product_id'];

                if ($product['product_amt'] > 0) {
                    $product_amt = $product['product_amt'] - 1;
                    $upd = Product::updateAmt($item['product_id'], $product_amt);
                }

                if ($item['split_var'] != null) {
                    $write = self::WriteConversion($item['product_id'], $item['split_var'], $order['order_id']);
                }

                // Создание купона на скидку
                $promo = Product::getAutoPromoByID($item['product_id'], 1); // Получили данные купона
                if ($promo && $promo['products'] && $promo['products'] != 'N;') {
                    Product::createCoupon($promo, $date, $rand_str, $order['client_email']);
                }

                // Отсылка пинкодов
                $checkBL = User::searchEmailinBL($order['client_email']); // Проверка емейла в чёрном спсике
                if ($checkBL == 0) {
                    if (!empty($product['pincodes']) && !$next) {
                        $pins = explode("\r\n", $product['pincodes']);
                        $pincode = $pins[0]; // пиг код

                        // Удалить пин код
                        unset($pins[0]);
                        $pin_count = count($pins);
                        $str = implode("\r\n", $pins);

                        $upd = Product::UpdatePincodes($item['product_id'], $str, $pin_count, $setting['admin_email']);
                    } else {
                        $pincode = null;
                    }
                } else {
                    $pincode = null;
                    $text = "<p>Был произведён заказ ID = ".$order['order_id'].", пользователем из чёрного списка, <br />
                    Имя: ".$order['client_name']."<br />Email: ".$order['client_email'].
                    "<p>Если к продукту прикладывается пин код для активации, то он не был отправлен.</p>";
                    if (!$sendCheckBL) {
                        $sendCheckBL = Email::SendMessageToBlank($setting['admin_email'], 'Admin', 'Заказ из чёрного списка', $text);
                    }
                }


                // Письмо с продуктом
                if ($product['send_pass'] != 0) {
					$subject = $product['letter_subject'];
                    $product_letter = $product['letter'];

                    if ($order['installment_map_id'] != 0) {
                        // Если платежей по рассрочке было больше 1, то отсылать письмо заказа не нужно.

                        $pay_actions = Installment::getCountPays($installment_map_data['pay_actions']);
                        if ($pay_actions >= 2) {
                            $installment_letters = unserialize(base64_decode($installment_data['letters']));
                            $product_letter = $installment_letters['letter_pay'];
                            $subject = $installment_letters['subject_pay'];
                        }
                    }

                    if (!empty($product_letter)) {
						Email::SendOrder($order['order_date'], $product_letter, $product['product_name'], $order['client_name'],
                            $order['client_email'], $item['price'], $pincode, $subject
                        );
					}
				}

                // Записать отправленный пин код, если есть
                if ($pincode != null) {
                    $status = 1;
                    self::UpdateOrderItem($item['order_item_id'], $status, $pincode);
                }

                // КАСТОМНОЕ ПИСЬМО МЕНЕДЖЕРУ
                if ($product['manager_letter'] != null) {
                    $manager_letter = unserialize(base64_decode($product['manager_letter']));

                    if (isset($manager_letter['email_manager']) && !empty($manager_letter['email_manager'])) {
                        $subj_manager = isset($manager_letter['subj_manager']) ? $manager_letter['subj_manager'] : null;
                        $letter_manager = isset($manager_letter['letter_manager']) ? $manager_letter['letter_manager'] : null;

                        $send_custom = Email::sendCustomLetterForManager($manager_letter['email_manager'],
                            $subj_manager, $letter_manager, $order
                        );
                    }
                }


                // АВТОРСКИЕ И ПАРТНЁРСКИЕ КОМИССИИ
                if ($extension) {
                    $total_aff = $item['price'];
                    $aff_comiss = array();

                    if ($order['partner_id'] != null) { // Если в заказе партнёр есть, то РАСЧЁТ партнёрских
                        if ($product['run_aff'] == 1) { // если партнёрка включена для этого продукта
							$partner_id = $order['partner_id'];

							// Комиссия для партнёра 1 уровня
							$partner = Aff::getPartnerReq($partner_id); // получаем данные партнёра
                            $partner_status = Aff::PartnerVerify($partner_id);

                            if ($partner && $partner_status) {
                                // Особый режим партнёрки
                                $run_aff = Aff::SpecAff($total_aff, $partner_id, $order, $item['product_id']);
                            

                                if (isset($run_aff) && $run_aff === false) {
        							
                                    $aff_comiss[1] = Aff::getPartnerComiss($partner, $product, $item, $aff_params);
                                    if($aff_comiss[1] == false) continue;
                                    
                                } else {
                                    $aff_comiss[1] = $run_aff;
                                }
                            }

							$total_aff = $total_aff - $aff_comiss[1]; // остаток суммы с вычтенной комиссией партнёра
                            $aff_transact1 = Aff::PartnerTransaction($partner_id, $order['order_id'], $item['product_id'],
                                $aff_comiss[1], 0, 1, $order['client_email']
                            );

                            if ($aff_comiss[1] > 0) {
                                $send1 = Aff::SendPartnerTransaction($partner_id, $order['order_date'], $aff_comiss[1], 0);
                                $partners_payouts += $aff_comiss[1];
                            }

                            // Считаем комиссию для 2 ур.
                            if ($aff_params['params']['aff_2_level'] > 0) {
                                $partner2 = User::getUserById($partner_id);

                                if ($partner['ref_id'] != 0 || $partner2['from_id'] != 0 ) {
                                    if ($partner2['from_id'] != 0) {
                                        $partner2_id = $partner2['from_id'];
                                    } elseif ($partner['ref_id'] != 0) {
                                        $partner2_id = $partner['ref_id'];
                                    }
                                    
                                    $partner_status = Aff::PartnerVerify($partner2_id);
                                    if(!$partner_status) continue;

                                    $aff_comiss[2] = round(($item['price']/100) * $aff_params['params']['aff_2_level']); // комиссия для партнёра 2 ур.
                                    $total_aff = $total_aff - $aff_comiss[2]; // остаток суммы с вычтенной комиссией партнёров 1 и 2 ур.

                                    $aff_transact2 = Aff::PartnerTransaction($partner2_id, $order['order_id'], $item['product_id'],
                                        $aff_comiss[2], 0, 1, $order['client_email']
                                    );

                                    if ($aff_comiss[2] > 0) {
                                        $send2 = Aff::SendPartnerTransaction($partner2_id, $order['order_date'], $aff_comiss[2], 1);
                                        $partners_payouts += $aff_comiss[2];
                                    }

                                    // Считаем комиссию 3-ур.
                                    if ($aff_params['params']['aff_3_level'] > 0) {
                                        $data2 = Aff::getPartnerReq($partner2_id);

										if ($data2['ref_id'] != 0) {
				                            
                                            $partner_status = Aff::PartnerVerify($data2['ref_id']);
                                            if(!$partner_status) continue;
                                            
											$aff_comiss[3] = round(($item['price']/100) * $aff_params['params']['aff_3_level']); // комисси ядля партнёра 3 ур.
											$total_aff = $total_aff - $aff_comiss[3]; // остаток суммы с вычтенной комиссией партнёров 1,2 и 3 ур.

											$aff_transact3 = Aff::PartnerTransaction($data2['ref_id'], $order['order_id'], $item['product_id'], $aff_comiss[3], 0, 1, $order['client_email']);

											if ($aff_comiss[3] > 0) {
                                                $send3 = Aff::SendPartnerTransaction($data2['ref_id'], $order['order_date'],
                                                    $aff_comiss[3], 1
                                                );
                                                $partners_payouts += $aff_comiss[3];
                                            }
										}
                                    }
								}
							}
						}
                    } elseif ($product['run_aff'] == 1 && $client_data && $client_data['from_id'] != null) { // Проверить наличие from_id у клиента, если он существует
                        $partner_id = $client_data['from_id'];

                        // комиссия для партнёра 1 уровня
                        $partner = Aff::getPartnerReq($partner_id); // получаем данные партнёра
                        $partner_status = Aff::PartnerVerify($partner_id);
                        
                        if($partner && $partner_status){
                            
                            $run_aff = Aff::SpecAff($total_aff, $partner_id, $order, $item['product_id']);
                            
                            if (isset($run_aff) && $run_aff === false) {
                                
                                $aff_comiss[1] = Aff::getPartnerComiss($partner, $product, $item, $aff_params);
                                if($aff_comiss[1] == false) continue;
                                
                            } else {
                                $aff_comiss[1] = $run_aff;
                            }
                        }

                        $total_aff = $total_aff - $aff_comiss[1]; // остаток суммы с вычтенной комиссией партнёра

                        $aff_transact1 = Aff::PartnerTransaction($partner_id, $order['order_id'], $item['product_id'],
                            $aff_comiss[1], 0, 1, $order['client_email']
                        );

                        if ($aff_comiss[1] > 0) {
                            $send1 = Aff::SendPartnerTransaction($partner_id, $order['order_date'], $aff_comiss[1], 1);
                            $partners_payouts += $aff_comiss[1];
                        }

                        // Считаем комиссию для 2 ур.
                        if ($aff_params['params']['aff_2_level'] != 0) {
                            $data = Aff::getPartnerReq($partner_id);

                            if ($data['ref_id'] != 0) {
                                $aff_comiss[2] = round(($item['price']/100) * $aff_params['params']['aff_2_level']); // комиссия для партнёра 2 ур.
                                $total_aff = $total_aff - $aff_comiss[2]; // остаток суммы с вычтенной комиссией партнёров 1 и 2 ур.


                                $aff_transact2 = Aff::PartnerTransaction($data['ref_id'], $order['order_id'],
                                    $item['product_id'], $aff_comiss[2], 0, 1, $order['client_email']
                                );

                                if ($aff_comiss[2] > 0) {
                                    $send2 = Aff::SendPartnerTransaction($data['ref_id'], $order['order_date'], $aff_comiss[2], 1);
                                    $partners_payouts += $aff_comiss[2];
                                }

                                // Считаем комиссию 3-ур.
                                if ($aff_params['params']['aff_3_level'] != 0) {
                                    $data2 = Aff::getPartnerReq($data['ref_id']);

                                    if ($data2['ref_id'] != 0) {
                                        $partner3_id = $data2['ref_id'];
                                        $aff_comiss[3] = round(($item['price']/100) * $aff_params['params']['aff_3_level']); // комисси ядля партнёра 3 ур.
                                        $total_aff = $total_aff - $aff_comiss[3]; // остаток суммы с вычтенной комиссией партнёров 1,2 и 3 ур.

                                        $aff_transact3 = Aff::PartnerTransaction($partner3_id, $order['order_id'], $item['product_id'], $aff_comiss[3], 0, 1, $order['client_email']);
                                        if ($aff_comiss[3] > 0) {
                                            $send3 = Aff::SendPartnerTransaction($partner3_id, $order['order_date'], $aff_comiss[3], 1);
                                            $partners_payouts += $aff_comiss[3];
                                        }
                                    }
                                }
                            }
                        }

                    }

                    // Если авторы есть
                    if ($product['author1'] != null || $product['author2'] != null || $product['author3'] != null) {
                        // Расчёт авторских
                        $authors_calc = Aff::AuthorComissCalc($order, $product, $item, $total_aff);
                    }
                }

                // Подписка на членство
                $membership = System::CheckExtensension('membership', 1);
                if ($membership == true && $client == true && !empty($product['subscription_id'])){
                    
                    // Если обычный платёж
				    if ($order['installment_map_id'] == 0) Member::addMember($product['subscription_id'], $client['user_id'], 1, $subscription_id, $order['subs_id']);
                    
                    else { // Если платёж по рассрочке
                        if($installment_map['pay_actions'] == null) {
                            
                            Member::addMember($product['subscription_id'], $client['user_id'], 1, $subscription_id, $order['subs_id']);
                        }
                    }
                }

                // РАССЫЛКА
                $responder = System::CheckExtensension('responder', 1);
                if ($responder && !empty($product['delivery_sub'])) {
                    $responder_setting = unserialize(Responder::getResponderSetting());
                    $delivery_sub_arr = unserialize($product['delivery_sub']);
                    foreach($delivery_sub_arr as $delivery_id) {
                        $delivery = Responder::getDeliveryData($delivery_id); // получил данные рассылки

                        $time = time();
                        $confirmed = $delivery['confirmation'] == 1 ? 0 : $time;
                        $cancelled = 0;
                        $spam = 0;
                        $subs_key = md5($order['client_email'] . $time);

                        // записать подписчика в базу
                        $add_subs = Responder::addSubsToMap($delivery_id, $order['client_email'], $order['client_name'], $order['client_phone'], $time, $subs_key, $confirmed, $cancelled, $spam, $order['visit_param'], $responder_setting, $setting);
                    }
                    if ($product['delivery_unsub'] != null) {
                        $delivery_unsub_arr = unserialize($product['delivery_unsub']);

                        foreach($delivery_unsub_arr as $delivery_uns_id) {
                            // Удалить запись в карте подписок
                            $del_sub = Responder::DeleteSubsRow($order['client_email'], $delivery_uns_id);
                            $del_letters = Responder::DeleteTaskByEmail($order['client_email'], $delivery_uns_id);
                        }
                    }

                }

                // список продуктов для квитанции
                $order_items .= '<tr><td style="text-align: left;">'.$product['product_name'].'</td><td>1</td><td style="text-align: right">'.$item['price'].' '. $setting['currency'].'</td></tr>';

                $product = Product::getProductDataForSendOrder($item['product_id']);

                // Удаление групп для пользователя
                if ($product['del_group_id']) {
                    User::deleteUserGroupsFromList($client['user_id'], $product['del_group_id']);
                }
    
                // Добавление групп для пользователя при рассрчоке и БЕЗ
                if ($product['group_id'] != 0 && ($order['installment_map_id'] == 0 || $product['installment_addgroups'] == 0)) {
                    $add_groups = explode(",", $product['group_id']);
                    foreach ($add_groups as $group) {
                        User::WriteUserGroup($client['user_id'], $group);
                    }
                }
            }

            $upd_order_data = Order::updateOrderData($order['order_id'], $partner_id, $partner2_id, $partner3_id, $partners_payouts);

            // TODO (Пока работает) Здесь проверяем включено ли расширение Тренинги 2.0 и находим доступные тренинги по
            // группам или подписке пользователя и смотрим есть ли авто-распределение по кураторам.
            $training = System::CheckExtensension('training', 1);
            if ($training) {
                $user_groups = $client['user_id'] ? User::getGroupByUser($client['user_id']) : false;
                $user_planes = $client['user_id'] ? Member::getPlanesByUser($client['user_id'], 1) : false;

                if ($user_groups || $user_planes) {
                    $filter = [
                        'user_groups' => $user_groups,
                        'user_planes' => $user_planes
                    ];

                    $training_list = $client['user_id'] ? Training::getTrainingList(null, null, $filter, null) : null;
                    if ($training_list) {
                        foreach($training_list as $training) {
                            if ($training['curators_auto_assign']==1) {
                                self::AssignUserToCurator($client['user_id'], $training);
                            }
                        }
                    }
                }
            }

            OrderTask::addTask($order['order_id'], OrderTask::STAGE_ACC_PAY); // добавление задач для крона по заказу

            // Отправка квитанции, если включено в настройках и сумма заказа более 0 рублей
            if ($setting['strict_report'] == 1) {
                if ($total_summ > 0) {
                    $send = Email::SendStrictReport($order['client_name'], $order['client_email'], $order['order_date'],
                        $date, $total_summ, $setting, $order_items
                    );
                }
            }

            // Отправить письмо админу (№ заказа, имя клиента, сумма, партнёр и т.д)
            $notify_admin_free = isset(json_decode($setting['params'])->disable_notify_admin_to_free_orders) ? json_decode($setting['params'])->disable_notify_admin_to_free_orders : 0;
            if ($total_summ > 0) {  
                $adm = Email::SendOrderToAdmin($order['order_date'], $order['client_name'], $order['client_email'], $total_summ,
                    $order['partner_id'], $order['order_id'], $order['payment_id']);
            } else {
                if (empty($notify_admin_free)) {
                    $adm = Email::SendOrderToAdmin($order['order_date'], $order['client_name'], $order['client_email'], $total_summ,
                        $order['partner_id'], $order['order_id'], $order['payment_id']);
                } else {
                    $adm = true;
                }
            }

            $delete_duplicate = self::removeDuplicateOrders($order['client_email'], $order['product_id']);

            return $adm ? true : false;
        } else {
            return false;
        }
    }



    // ОБНОВИТЬ ДАННЫЕ ЗАКАЗА ПРИ ОБРАБОТКЕ
    public static function updateOrderData($order_id, $partner_id, $partner2_id, $partner3_id, $partners_payouts)
    {
        $db = Db::getConnection();

        $result = $db->query(" SELECT * FROM ".PREFICS."orders WHERE order_id = $order_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){

            $order_info = unserialize(base64_decode($data['order_info']));

            if($partner2_id) $order_info['aff2'] = $partner2_id;
            if($partner3_id) $order_info['aff3'] = $partner3_id;
            if($partners_payouts > 0) $order_info['aff_summ'] = $partners_payouts;

            $order_info = base64_encode(serialize($order_info));

            $sql = 'UPDATE '.PREFICS.'orders SET partner_id = :partner_id, order_info = :order_info WHERE order_id = '.$order_id;
            $result = $db->prepare($sql);
            $result->bindParam(':order_info', $order_info, PDO::PARAM_STR);
            $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
            return $result->execute();
        }
    }



	// ОБНОВИТЬ КОММЕНТ К ЗАКАЗУ
    public static function updateAdminCommentByOrder($order_id, $comment, $map_item_id)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET admin_comment = :admin_comment, installment_map_id = :map_id WHERE order_id = '.$order_id;
        $result = $db->prepare($sql);
        $result->bindParam(':admin_comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':map_id', $map_item_id, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОБНОВИТЬ КАРТУ ПРИ ДОСРОЧНОМ ПОГАШЕНИИ
    public static function updateMapFromAhead($map_id, $order_id, $admin_comment, $reset = 0)
    {
        $db = Db::getConnection();
        if ($reset == 1) $sql = 'UPDATE '.PREFICS.'installment_map SET ahead_id = :ahead_id WHERE id = '.$map_id;
        else $sql = 'UPDATE '.PREFICS.'installment_map SET ahead_id = :ahead_id WHERE id = '.$map_id.'; UPDATE '.PREFICS.'orders SET admin_comment = :admin_comment WHERE order_id = '.$order_id;
        $result = $db->prepare($sql);
        $result->bindParam(':ahead_id', $order_id, PDO::PARAM_INT);
        if ($reset == 0) $result->bindParam(':admin_comment', $admin_comment, PDO::PARAM_STR);
        return $result->execute();
    }


    /**
     * ПОИСК РАССРОЧЕК ПО EMAIL
     * @param $email
     * @param null $status
     * @return array|bool
     */
    public static function searchInstallmentByEmail($email, $status = null) {

        $db = Db::getConnection();
        $query = "SELECT id, summ, status, max_periods, next_pay, pay_actions, next_order, installment_id
                  FROM ".PREFICS."installment_map WHERE email = '$email' AND status IN (1,9) ORDER BY id DESC";
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;

    }

    // ОБНОВЛЕНИЕ ЗАПИСИ В КАРТЕ
    public static function updateInstalMapItem($id, $comment, $next_pay, $status, $summ, $email = false)
    {
        $db = Db::getConnection();
        if ($email) $sql = 'UPDATE '.PREFICS.'installment_map SET comment = :comment, next_pay = :next_pay, status = :status, summ = :summ, email = :email WHERE id = '.$id;
        else $sql = 'UPDATE '.PREFICS.'installment_map SET comment = :comment, next_pay = :next_pay, status = :status, summ = :summ WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':next_pay', $next_pay, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        if ($email) $result->bindParam(':email', $email, PDO::PARAM_STR);
        return $result->execute();
    }


    // ОБНОВЛЕНИЕ КОЛ_ВА ОТПРАВЛЕННЫХ УВЕДОМЛЕНИЙ
    public static function updateNotifCount($id, $number, $order_date = null, $expired = 0)
    {
        $db = Db::getConnection();
        if ($order_date != null) $sql = 'UPDATE '.PREFICS.'installment_map SET notif = :notif, next_order = :next_order WHERE id = '.$id;
        elseif ($order_date == null && $expired == 1) $sql = 'UPDATE '.PREFICS.'installment_map SET after_notif = :notif WHERE id = '.$id;
        else $sql = 'UPDATE '.PREFICS.'installment_map SET notif = :notif WHERE id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':notif', $number, PDO::PARAM_INT);
        if ($order_date != null) $result->bindParam(':next_order', $order_date, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ПОИСК ПОДХОДЯЩИХ ПЛАТЕЖЕЙ ПО РАССРОЧКЕ
     * @param $installment_id
     * @param $kick
     * @param $status
     * @param $notif
     * @param int $expired
     * @return array|bool
     */
    public static function searchInstallFromMap($installment_id, $kick, $status, $notif, $expired = 0)
    {
        $sql = "SELECT * FROM ".PREFICS."installment_map WHERE installment_id = $installment_id AND next_pay < $kick";
        $sql .= $expired == 0 ? " AND status = $status AND notif = $notif" : " AND status IN (1, 9) AND after_notif = $notif";

        $db = Db::getConnection();
        $result = $db->query($sql);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    // ПОИСК ИСТЕКШИХ РАССРОЧЕК
    public static function searchExpireInstallments($expired)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."installment_map WHERE next_pay < $expired AND status = 1";
        $result = $db->query($sql);
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['id'] = $row['id'];
            $data[$i]['email'] = $row['email'];
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['installment_id'] = $row['installment_id'];
            $i++;
        }
        if (isset($data) && !empty($data)) return $data;
        else return false;
    }


    /**
     * СОЗДАНИЕ ЗАКАЗА ДЛЯ ОПЛАТЫ РАССРОЧКИ
     * (при сработке крона, при досрочном погашении)
     * @param $order_id
     * @param $sum
     * @param $email
     * @param $now
     * @param $install_map_id
     * @param null $instalment_id
     * @return bool|mixed
     */
    public static function createNewOrderFromInstallment($order_id, $sum, $email, $now, $install_map_id, $instalment_id = null)
    {
        // Получить данные первого заказа (продукты)
        $order = self::getOrderDataByID($order_id, 1);
        if (!$order) {
            $setting = System::getSetting();
            $send = Email::SendMessageToBlank($setting['admin_email'], 'School-Master', 'School-Master', " При создании платежа рассрочки по договору $install_map_id не найден заказ с $order_id <br />Проверьте данные по юзеру с $email.");

            return false;
        }


		while (Order::checkOrderDate($now)) {
            $now += 1;
        }

		// Если продукт id не записан в заказ, то берём из items'a
        if ($order['product_id'] == null) {
            $items = Order::getOrderItems($order_id);
            $order['product_id'] = $items ? $items[count($items)-1]['product_id'] : 0;
        }

        $param = $now.';0;;/intstallment';
        $status = 5;
        $base_id = null;
        $var = null;
        $type_id = 1;
        $product_name = 'Рассрочка';
        $ip = null;

        // Создать заказ с полной стоимостью рассрочки
        $add_order_id = self::addOrder($order['product_id'], $sum, $order['client_name'], $order['client_email'], $order['client_phone'],
            $order['client_index'], $order['client_city'], $order['client_address'], $order['client_comment'], $param, $order['partner_id'],
            $now, $order['sale_id'], $status, $base_id, $var, $type_id, $product_name, $ip, 0, null, null,
            null, $install_map_id
        );

        if ($add_order_id) {
            OrderTask::addTask($add_order_id, OrderTask::STAGE_INSTALLMENT_ACC_STAT); // добавление задач для крона по заказу
        }

        return $add_order_id ? $add_order_id : false;
    }


    // ЗАВЕРШИТЬ РАССРОЧКУ
    public static function endInstallment($order, $installment_map_data, $type)
    {
        // если тип 1 - значит рассрочка оплачена
        // если тип 0 - значит рассрчока просрочена

        $setting = System::getSetting();

        // Получить данные клиента
        $user = User::getUserDataByEmail($order['client_email']);
		if (!$user) {
            $subject = 'Клиент для рассрочки не найден';
            $client_email = $order['client_email'];
            $map_id = $order['installment_map_id'];
            $text = "<p>School Master не может найти клиента для рассрочки, проверьте данные клиента, возможно кто-то сменил его емейл.<br />Ищем по емейлу: $client_email<br />Договор рассрочки: $map_id </p>";
            $admin_send = Email::SendMessageToBlank($setting['admin_email'], '', $subject, $text);
			return false;
        }

        $installment_data = Product::getInstallmentData($installment_map_data['installment_id']);

        $increase_pay = $installment_data['increase'] / $installment_data['max_periods'];

        // Получить продукты из заказа
        $order_items = self::getOrderItems($order['order_id']);
        if ($order_items) {

            foreach($order_items as $item) {

                $product = Product::getProductById($item['product_id']);
                if ($product['installment_action'] != null) {
                    $installment_action = unserialize(base64_decode($product['installment_action']));

                    if ($type == 1) {
                        // добавляем группы
                        if (!empty($installment_action['add_group'])) {
                            foreach($installment_action['add_group'] as $group_id) {
                                $write = User::WriteUserGroup($user['user_id'], $group_id);
                            }
                        }

                        // добавляем план подписки
                        if (!empty($installment_action['add_plane'])) {
                            $add = Member::addMember($installment_action['add_plane'], $user['user_id'], 1, null);
                        }

                        $letters = unserialize(base64_decode($installment_data['letters']));
                        if (!empty($letters['letter_client_end'])) {
                            $send = Email::SendClientNotifAboutInstallment($order['client_email'], $order['client_name'], $order_date = null, $letters['subject_client_end'], $letters['letter_client_end'], $link = null);
                        }




                        // Письмо админу
                        $subject = 'Всё ок, рассрочка оплачена';
                        $text = '<p>Рассрочка полностью оплачена</p><p>Клиент </p>'.$order['client_email'];
                        $text .= '<br /><a href="'.$setting['script_url'].'/admin/orders/edit/'.$order['order_id'].'">Заказ '.$order['order_id'].'</a>';
                        $text .= '</p><p>Это автоматическое уведомление от системы School-Master</p>';
                        $send = Email::SendMessageToBlank($setting['admin_email'], 'name', $subject, $text);

                    } else {
                        if (!empty($installment_action['del_group'])) {
                            $del_groups = User::deleteUserGroupsFromList($user['user_id'], $installment_action['del_group']); // удаляем группы
                        }

                        if (!empty($installment_action['del_plane'])) { // удаляем планы подписки
                            foreach($installment_action['del_plane'] as $plane_id) {
                                $del = Member::delMemberByEmail($user['user_id'], $plane_id);
                            }
                        }

                        // Увеличиваем сумму, если есть штраф
                        if ($installment_data['sanctions'] > 0) {

                            $map_data = Order::getInstallmentMapData($order['installment_map_id']); // получаем данные из карты рассрочек

                            // Получаем сумму и кол-во успешных платежей
                            $pay_actions = unserialize(base64_decode($map_data['pay_actions']));
                            if (!empty($map_data['pay_actions'])) {
                                $i = 0;
                                $pay_summ = 0;
                                foreach($pay_actions as $pay) {
                                    $pay_summ = $pay_summ + $pay['summ'];
                                    $i++;
                                }
                            }

                            $summ = $map_data['summ'] + $installment_data['sanctions']; // увеличиваем сумму рассрочки
                            $ahead_id = 0; // обнуляем ahead_id для досрочного погашения в будущем

                            $db = Db::getConnection();
                            $sql = 'UPDATE '.PREFICS.'installment_map SET summ = :summ, ahead_id = :ahead_id WHERE id = '.$map_data['id'];
                            $result = $db->prepare($sql);
                            $result->bindParam(':summ', $summ, PDO::PARAM_INT);
                            $result->bindParam(':ahead_id', $ahead_id, PDO::PARAM_INT);
                            $result->execute();

                            // Увеличиваем сумму уже созданного заказа
                            if ($map_data['next_order'] != 0) {
                                $admin_comment = 'Оплата рассрочки при просрочке';
                                $next_pay_order = Order::getOrderData($map_data['next_order'], 0, 1);

                                if($next_pay_order){
                                    $order_summ = false;
                                    $preupd = true; // работаем уже со сниженной стоимостью заказа
                                    $expired = 1; // надо уменьшить сумму заказа на $increase_pay
                                    $upd = self::updateSummForInstallment($next_pay_order['order_id'], $installment_data['other_pay'], $admin_comment, $map_data['id'], $order_summ, $preupd, $increase_pay, $expired);
                                }
                            }
                        }

                        // Письмо админу
                        $subject = 'Рассрочка просрочена';
                        $text = '<p>Рассрочка просрочена</p><p>Клиент </p>'.$order['client_email'];
                        $text .= '<br /><a href="'.$setting['script_url'].'/admin/orders/edit/'.$order['order_id'].'">Заказ '.$order['order_id'].'</a>';
                        $text .= '</p><p>Это автоматическое уведомление от системы School-Master</p>';
                        $send = Email::SendMessageToBlank($setting['admin_email'], 'name', $subject, $text);

                        if ($send) return true;

                    }



                }

            }

        }
    }


    // ИЗМЕНИТЬ СТАТУС РАССРОЧКИ
    public static function updateInstallMentStatus($id, $status)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'installment_map SET status = :status WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }


	// УДАЛИТЬ ДОСРОЧНОЕ ПОГАШЕНИЕ ИЗ КАРТЫ
    public static function deleteAheadInMap($map_id)
    {
        $db = Db::getConnection();
        $null = 0;
        $sql = 'UPDATE '.PREFICS.'installment_map SET ahead_id = :ahead WHERE id = '.$map_id;
        $result = $db->prepare($sql);
        $result->bindParam(':ahead', $null, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // УДАЛИТЬ ПЛАТЁЖ ПО РАССРОЧКЕ
    public static function deleteNextOrderInMap($map_id)
    {
        $db = Db::getConnection();
        $zero = 0;
        $sql = 'UPDATE '.PREFICS.'installment_map SET next_order = :zero, notif = :zero WHERE id = '.$map_id;
        $result = $db->prepare($sql);
        $result->bindParam(':zero', $zero, PDO::PARAM_INT);
        return $result->execute();
    }


    // УДАЛИТЬ ДОГОВОР РАССРОЧКИ
    public static function deleteInstallMap($map_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'installment_map WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $map_id, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ИЗ КАРТЫ РАССРОЧЕК
     * @param $id
     * @return bool|mixed
     */
    public static function getInstallmentMapData($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."installment_map WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data)? $data :false;
    }


    // ИЗМЕНЕНИЯ СТАТУСА ЗАКАЗА В РАССРОЧКЕ
    public static function updateStatusInstallment($order_date, $status)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET status = :status WHERE order_date = '.$order_date;
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }





    // ИЗМЕНЕНИЕ СТОИМОСТИ ЗАКАЗА и ПРОДУКТОВ ВНУТРИ
    public static function updateSummForInstallment($order_id, $percent, $admin_comment, $map_id, $order_summ = false, $preupd = false, $increase_pay = 0, $expired = false, $first = false)
    {
        // Именить сумму для продуктов
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."order_items WHERE order_id = ".$order_id);
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['order_item_id'] = $row['order_item_id'];
            $data[$i]['price'] = $row['price'];
            $i++;
        }

        if (isset($data) && !empty($data)) {
            $s = 0;
            $full_summ = 0;
            $increase_pay = $increase_pay / count($data); // разделили платёж на кол-во продуктов, чтобы прибавить к их цене

            foreach($data as $item) {
                if ($first) $new_summ = round(($item['price'] / 100) * $percent + $increase_pay);  // обычный заказ
                // Новый расчёт, для остальных платежей, кроме первого

                if (!$first) {
                    $map_item = Order::getInstallmentMapData($map_id); // получили данные карты

                    $installment = Product::getInstallmentData($map_item['installment_id']); // получили настройки рассрочки

                    if ($expired) $minus = $installment['sanctions'];
                    else $minus = 0;

                    $summ = $map_item['summ'] - $minus; // полная сумма первого заказа

                    $full_install_summ = $summ + $minus;

                    // получаем сумму платежей
                    $pay_actions = unserialize(base64_decode($map_item['pay_actions']));
                        if ($pay_actions) {
                                $pay_summ = 0;
                                $pay_count = 0;
                                foreach($pay_actions as $action) {
                                    $pay_summ = $pay_summ + $action['summ'];
                                    $pay_count++;
                                }
                        }

                    $new_summ = ($full_install_summ - $pay_summ) / ($installment['max_periods'] - $pay_count);
                }

                $sql = 'UPDATE '.PREFICS.'order_items SET price = :price WHERE order_item_id = '.$item['order_item_id'];
                $result = $db->prepare($sql);
                $result->bindParam(':price', $new_summ, PDO::PARAM_INT);
                $res = $result->execute();
                $s++;
                $full_summ = $full_summ + $new_summ;
            }

            // Дописать комментарий к заказу
            if ($order_summ) $sql = 'UPDATE '.PREFICS.'orders SET admin_comment = :admin_comment, installment_map_id = :map_id WHERE order_id = '.$order_id;
            else $sql = 'UPDATE '.PREFICS.'orders SET admin_comment = :admin_comment, installment_map_id = :map_id, summ = :summ WHERE order_id = '.$order_id;
            $result = $db->prepare($sql);
            $result->bindParam(':admin_comment', $admin_comment, PDO::PARAM_STR);
            $result->bindParam(':map_id', $map_id, PDO::PARAM_INT);
            if (!$order_summ)$result->bindParam(':summ', $full_summ, PDO::PARAM_INT);
            $res = $result->execute();

            if ($i == $s) return true;
            else return false;

        } else return false;


    }



    // ПОИСК и удаление ДУБЛЕЙ ЗАКАЗА
    public static function removeDuplicateOrders($email, $product_id)
    {
        $db = Db::getConnection();
        $now = time();
        $date = $now - 259200;
        $sql = 'DELETE FROM '.PREFICS.'orders WHERE product_id = :product_id AND client_email = :email AND status = 0 AND order_date > '.$date;
        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        return $result->execute();
    }


    // ПРОВЕРИТЬ СУЩЕСТВОАНИЕ ЗАКАЗА ПО order_date
    public static function checkOrderDate($order_date)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE order_date = $order_date");
        $count = $result->fetch();
        if ($count[0] > 0) return $count[0];
        else return false;
    }

    // ПОИСК НЕОПЛАЧЕННЫХ ЗАКАЗОВ
    public static function searchNoPaidOrders($time, $num, $sms = null, $prod_ids = '', $type = 1)
    {
        $db = Db::getConnection();
        $time2 = $time - 86400;
        $sql = "SELECT o.order_id, o.order_date, o.client_name, o.client_phone, o.client_email FROM ".PREFICS."orders AS o
                LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                WHERE o.status = 0 AND o.summ > 0 AND o.order_date < $time AND o.order_date > $time2";

        $sql .= $sms ? " AND o.remind_sms = $num" : " AND o.remind_letter = $num";

        if ($prod_ids) {
            $sql .= " AND oi.product_id " . ($type == 1 ? "IN" : "NOT IN") . " ($prod_ids)";
        }

        $result = $db->query($sql);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }

    // ОБНОВЛЕНИЕ СТАТУСА ДОЖИМАЮЩЕГО ПИСЬМА  ЗАКАЗА
    public static function updateRemindLetterInOrder($order_id, $status, $sms = false)
    {
        $db = Db::getConnection();
        if ($sms == false) $sql = 'UPDATE '.PREFICS.'orders SET remind_letter = :status WHERE order_id = '.$order_id;
        else $sql = 'UPDATE '.PREFICS.'orders SET remind_sms = :status WHERE order_id = '.$order_id;
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОБНОВЛЕНИЕ ID канала у заказа
    public static function updateChannel_id($order_id, $channel_id)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET channel_id = :channel_id WHERE order_id = '.$order_id;
        $result = $db->prepare($sql);
        $result->bindParam(':channel_id', $channel_id, PDO::PARAM_INT);
        return $result->execute();
    }



    // ПОЛУЧИM ДАННЫЕ СПЛИТ ТЕСТА
    public static function getSplitTestData($product_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(split_id) FROM ".PREFICS."split_tests WHERE product_id = $product_id AND variant = 1");
        $count = $result->fetch();
        $var[1] = $count[0];

        $result = $db->query("SELECT COUNT(split_id) FROM ".PREFICS."split_tests WHERE product_id = $product_id AND variant = 2");
        $count = $result->fetch();
        $var[2] = $count[0];
        return $var;
    }




    // СПИСОК ВСЕХ ЗАКАЗОВ
    public static function getAllOrders()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT order_id FROM ".PREFICS."orders WHERE status = 1");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['order_id'] = $row['order_id'];
            $i++;
        }
        if (isset($data) && !empty($data)) return $data;
        else return false;
    }


    /**
     * ПОСЧИТАТЬ СТОИМОСТЬ ПРОДУКТОВ В ЗАКАЗЕ
     * @param $order_id
     * @return bool
     */
    public static function getOrderTotalSum($order_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(price) FROM ".PREFICS."order_items WHERE order_id = $order_id");
        $data = $result->fetch();

        return !empty($data) ? $data[0] : false;
    }


    /**
     * ПОСЧИТАТЬ СУММАРНУЮ СТОИМОСТЬ ПРОДУКТОВ В ЗАКАЗАХ
     * @return bool
     */
    public static function getOrdersTotalSum()
    {
        $db = Db::getConnection();
        $sql = "SELECT SUM(oi.price) FROM ".PREFICS."order_items AS oi
                LEFT JOIN ".PREFICS."orders AS o ON o.order_id = oi.order_id
                WHERE o.status = 1";

        $result = $db->query($sql);
        $data = $result->fetch();

        return !empty($data) ? $data[0] : false;
    }

    // ОБНОВИТЬ ЗАКАЗ ПРИ ОПЛАТЕ + записать время оплаты
    public static function UpdateOrderStatus ($order_date, $time, $payment_id = null)
    {
        $status = 1;
        $db = Db::getConnection();

        if ($payment_id != null) $sql = 'UPDATE '.PREFICS.'orders SET status = :status, payment_date = :time, payment_id = :payment_id WHERE order_date = :order_date';
        else $sql = 'UPDATE '.PREFICS.'orders SET status = :status, payment_date = :time WHERE order_date = :order_date';

        $result = $db->prepare($sql);
        $result->bindParam(':order_date', $order_date, PDO::PARAM_INT);
        $result->bindParam(':time', $time, PDO::PARAM_INT);
        if ($payment_id != null) $result->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->execute();

        $result = $db->query(" SELECT order_id FROM ".PREFICS."orders WHERE order_date = $order_date ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) {
            $sql = 'UPDATE '.PREFICS.'order_items SET status = :status WHERE order_id = '.$data['order_id'];
            $result = $db->prepare($sql);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            return $result->execute();
        }
    }


    /**
     * ОБНОВИТЬ СУММУ ЗАКАЗА
     * @param $order_id
     * @param $sum
     * @return bool
     */
    public static function updateOrderSum($order_id, $sum) {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET summ = :sum WHERE order_id = :order_id';
        $result = $db->prepare($sql);
        $result->bindParam(':sum', $sum, PDO::PARAM_INT);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // ОБНОВИТЬ | ОБНУЛИТЬ ДАТУ СКАЧИВАНИЯ ЗАКАЗА
    public static function UpdateOrderDwl ($order_date, $time)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET dwl_time = :time WHERE order_date = :order_date';
        $result = $db->prepare($sql);
        $result->bindParam(':order_date', $order_date, PDO::PARAM_INT);
        $result->bindParam(':time', $time, PDO::PARAM_INT);
        return $result->execute();
    }


    // Проверить существование продукта в заказе
    public static function ExistProductInOrder($order_id, $product_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT order_item_id, dwl_count FROM ".PREFICS."order_items WHERE order_id = $order_id AND product_id = $product_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data;
        else return false;
    }


    //
    public static function UpdateOrderDwlCount($item, $count)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'order_items SET dwl_count = :count WHERE order_item_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $item, PDO::PARAM_INT);
        $result->bindParam(':count', $count, PDO::PARAM_INT);
        return $result->execute();
    }



    // ДОПИСАТЬ К ПРОДУКТУ из ЗАКАЗА ПИНКОД, который были отправлены клиенту
    public static function UpdateOrderItem($item_id, $status, $pin)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'order_items SET status = :status, pincode = :pincode WHERE order_item_id = :item_id';
        $result = $db->prepare($sql);
        $result->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':pincode', $pin, PDO::PARAM_STR);
        return $result->execute();
    }




    // ОТПРАВИТЬ ЗАКАЗ КЛИЕНТУ ИЗ ЕГО КАБИНЕТА
    // Принимает массив с данными заказа и группами пользователя
    public static function getDwlOrder($order, $user_groups)
    {
        // Получить настройки
        $setting = System::getSetting();

        // Получить список продуктов в заказе
        $order_items = self::getOrderItems($order['order_id']);

        foreach($order_items as $item) {

            $product = Product::getProductDataForSendOrder($item['product_id']);
            if ($product) {

                $prod_groups = explode(",", $product['group_id']);
                $right = 0;
                foreach($prod_groups as $group) {
                    if (in_array($group, $user_groups)) $right = 1;
                    else $right = 0;
                }

                if ($right == 1) {
                    $send = Email::SendOrder($order['order_date'], $product['letter'], $product['product_name'],
                    $order['client_name'], $order['client_email'], $order['summ'], $pincode = 0);
                }

            }

        }

        if ($send == 1) return true;
        else return false;

    }


    /**
     * ПОЛУЧИТЬ СПИСОК ЗАКАЗОВ В АДМИНКУ
     * @param $page
     * @param $show_items
     * @param $is_pagination
     * @param null $status
     * @param null $email
     * @param null $number
     * @param null $start
     * @param null $finish
     * @param null $paid
     * @param null $product_id
     * @return array|bool
     */
    public static function getOrderAdminList($page, $show_items, $is_pagination, $status = null, $email = null,
        $number = null, $start = null, $finish = null, $paid = null, $product_id = null)
    {
        $clauses = [];
        if ($status !== null) {
            $statuses = ['ok' => 1, 'refund' => 9, 'no' => 0, 'check' => 2, 'inst' => 5, 'confirm' => 7];
            $clauses[] = 'o.status= ' . (isset($statuses[$status]) ? $statuses[$status] : $status);
        }
        if ($start != null) {
            $clauses[] = ($status == 1 ? 'o.payment_date' : 'o.order_date') . " >= $start";
        }
        if ($finish != null) {
            $clauses[] = ($status == 1 ? 'o.payment_date' : 'o.order_date') . " < $finish";
        }
        if ($paid == 1 || $paid == 2) {
            $clauses[] = $paid == 1 ? 'o.summ > 0' : 'o.summ = 0';
        }
        if ($number != null) {
            $clauses[] = "o.order_date = $number";
        }
        if ($email != null) {
            $clauses[] = "o.client_email LIKE '%$email%'";
        }
        if ($product_id != null) {
            $clauses[] = "oi.product_id = $product_id";
        }

        $where = !empty($clauses) ? (" WHERE " . implode(' AND ', $clauses)) : '';
        $sql = "SELECT o.* FROM ".PREFICS.'orders AS o';
        $sql.= $product_id != null ? ' LEFT JOIN '.PREFICS.'order_items AS oi ON oi.order_id = o.order_id' : '';
        $sql.= "$where GROUP BY o.order_id ORDER BY o.order_id DESC";

        $offset = ($page - 1) * $show_items;
        $sql .= $is_pagination ? " LIMIT $show_items OFFSET $offset" : '';

        $db = Db::getConnection();
        $result = $db->query($sql);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // ПОЛУЧИТЬ ЗАКАЗЫ ЗА СЕГОДНЯ
    public static function OrderToday($time)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."orders WHERE payment_date >= $time ORDER BY order_id DESC");

        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['order_date'] = $row['order_date'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['client_name'] = $row['client_name'];
            $data[$i]['client_email'] = $row['client_email'];
            $data[$i]['client_phone'] = $row['client_phone'];
            $data[$i]['client_city'] = $row['client_city'];

            $data[$i]['client_address'] = $row['client_address'];
            $data[$i]['client_index'] = $row['client_index'];
            $data[$i]['client_comment'] = $row['client_comment'];
            $data[$i]['admin_comment'] = $row['admin_comment'];
            $data[$i]['partner_id'] = $row['partner_id'];
            $data[$i++]['status'] = $row['status'];
        }

        return isset($data) && !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ЗАКАЗЫ КЛИЕНТА С УКАЗАННЫМ СТАТУСОМ, по умолчанию статус ЛЮБОЙ
     * @param $email
     * @param null $status
     * @return array|bool
     */
    public static function getUserOrders($email, $status = null)
    {
        $db = Db::getConnection();
        $where = "WHERE client_email = '$email'" . ($status !== null ? ' AND status = '.(int)$status : '');
        $result = $db->query("SELECT * FROM ".PREFICS."orders $where ORDER BY order_id DESC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    // ПОЛУЧИТЬ НЕОПЛАЧЕННЫЕ ЗАКАЗЫ КЛИЕНТА с неистёкшим сроком
    public static function getUserNopayOrders($email, $time, $now)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."orders WHERE client_email = '$email' AND status = 0 AND order_date > $time AND order_date < $now ORDER BY order_id DESC");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['order_date'] = $row['order_date'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['client_comment'] = $row['client_comment'];
            $data[$i]['partner_id'] = $row['partner_id'];
            $data[$i]['status'] = $row['status'];
            $i++;
        }

        return isset($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ЗАКАЗ В БД
     * @param $id
     * @param $summ
     * @param $name
     * @param $email
     * @param $phone
     * @param $index
     * @param $city
     * @param $address
     * @param $comment
     * @param $param
     * @param $partner_id
     * @param $date
     * @param $sale_id
     * @param $status
     * @param $base_id
     * @param $var
     * @param $type_id
     * @param $product_name
     * @param $ip
     * @param $remind_letter
     * @param null $surname
     * @param null $nick_telegram
     * @param null $nick_instagram
     * @param int $install_map_id
     * @param null $utm
     * @param int $is_recurrent
     * @param int $subs_id
     * @return mixed
     */
    public static function addOrder($id, $summ, $name, $email, $phone, $index, $city, $address, $comment, $param, $partner_id,
                                    $date, $sale_id, $status, $base_id, $var, $type_id, $product_name, $ip, $remind_letter,
                                    $surname = null, $nick_telegram = null, $nick_instagram = null, $install_map_id = 0,
                                    $utm = null, $is_recurrent = 0, $subs_id = 0)
    {
        $db = Db::getConnection();

        $order_info = [
            'surname' => $surname,
            'nick_telegram' => $nick_telegram,
            'nick_instagram' => $nick_instagram,
            'userId_YM' => isset($_COOKIE['_ym_uid']) ? $_COOKIE['_ym_uid'] : null,
            'userId_GA' => isset($_COOKIE['_gid']) ? $_COOKIE['_gid'] : null,
            'roistat_visitor' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
            'userId_FB' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
            'userId_FBс' => isset($_COOKIE['_fbс']) ? $_COOKIE['_fbс'] : null,
        ];
        $order_info = array_filter($order_info, 'strlen') ? base64_encode(serialize($order_info)) : null;

		$dwl_count = 0;
        // Получить ID рекламного канала
        $arr1 = explode(";", $param);
        $channel_id = isset($arr1[2]) && !empty($arr1[2]) ? $arr1[2] : 0;

        $sql = 'INSERT INTO '.PREFICS.'orders (order_date, product_id, summ, client_name, client_email, client_phone, client_city,
                    client_address, client_index, client_comment, sale_id, partner_id, status, base_id, visit_param, channel_id, ip,
                    remind_letter, order_info, installment_map_id, utm, is_recurrent, subs_id) 
                VALUES (:order_date, :product_id, :summ, :client_name, :client_email, :client_phone, :client_city, :client_address,
                    :client_index, :client_comment, :sale_id, :partner_id, :status, :base_id, :visit_param, :channel_id, :ip,
                    :remind_letter, :order_info, :installment_map_id, :utm, :is_recurrent, :subs_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_date', $date, PDO::PARAM_INT);
        $result->bindParam(':product_id', $id, PDO::PARAM_INT);
		$result->bindParam(':subs_id', $subs_id, PDO::PARAM_INT);

        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        $result->bindParam(':client_name', $name, PDO::PARAM_STR);
        $result->bindParam(':client_email', $email, PDO::PARAM_STR);
		$result->bindParam(':utm', $utm, PDO::PARAM_STR);

        $result->bindParam(':client_phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':client_city', $city, PDO::PARAM_STR);
        $result->bindParam(':client_address', $address, PDO::PARAM_STR);
        $result->bindParam(':client_index', $index, PDO::PARAM_STR);
        $result->bindParam(':client_comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':base_id', $base_id, PDO::PARAM_INT);
        $result->bindParam(':channel_id', $channel_id, PDO::PARAM_INT);
        $result->bindParam(':visit_param', $param, PDO::PARAM_STR);
        $result->bindParam(':ip', $ip, PDO::PARAM_STR);
        $result->bindParam(':remind_letter', $remind_letter, PDO::PARAM_INT);
        $result->bindParam(':order_info', $order_info, PDO::PARAM_STR);
        $result->bindParam(':installment_map_id', $install_map_id, PDO::PARAM_INT);
        $result->bindParam(':is_recurrent', $is_recurrent, PDO::PARAM_INT);
        $result->execute();

        // Получить ID созданного заказа
        $result = $db->query(" SELECT order_id FROM ".PREFICS."orders WHERE order_date = $date ");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            // Записать продукт
            $number = 1;
            $cast = 'main'; // Основной продукт с которого начался заказ
            $sql = 'INSERT INTO '.PREFICS.'order_items (order_id, product_id, type_id, number, price, status, cast, product_name, split_var, dwl_count ) 
                    VALUES (:order_id, :product_id, :type_id, :number, :price, :status, :cast, :product_name, :split_var, :dwl_count)';

            $result = $db->prepare($sql);
            $result->bindParam(':order_id', $data['order_id'], PDO::PARAM_INT);
            $result->bindParam(':product_id', $id, PDO::PARAM_INT);
            $result->bindParam(':type_id', $type_id, PDO::PARAM_INT);
            $result->bindParam(':number', $number, PDO::PARAM_INT);
            $result->bindParam(':price', $summ, PDO::PARAM_INT);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':cast', $cast, PDO::PARAM_STR);
            $result->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $result->bindParam(':split_var', $var, PDO::PARAM_INT);
			$result->bindParam(':dwl_count', $dwl_count, PDO::PARAM_INT);
            $result->execute();

            return $data['order_id'];
        }
    }


    /**
     * ДОБАВИТЬ ЗАКАЗ В БД в ручную
     * @param $id
     * @param $date
     * @param $summ
     * @param $name
     * @param $email
     * @param $phone
     * @param $city
     * @param $address
     * @param $index
     * @param $comment
     * @param $sale_id
     * @param null $partner_id
     * @param $status
     * @param $order_items
     * @param $price
     * @param null $base_id
     * @param null $channel_id
     * @param null $param
     * @param null $ip
     * @return bool
     */
    public static function addCustomOrder($id, $date, $summ, $name, $email, $phone, $city, $address, $index, $comment, $sale_id,
                                          $partner_id = null, $status, $order_items, $price, $base_id = null, $channel_id = null,
                                          $param = null, $ip = null) {


        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'orders (order_date, product_id, summ, client_name, client_email, client_phone, client_city,
                    client_address, client_index, admin_comment, sale_id, partner_id, status, base_id, visit_param, channel_id, ip) 
                VALUES (:order_date, :product_id, :summ, :client_name, :client_email, :client_phone, :client_city, :client_address,
                    :client_index, :admin_comment, :sale_id, :partner_id, :status, :base_id, :visit_param, :channel_id, :ip)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_date', $date, PDO::PARAM_INT);
        $result->bindParam(':product_id', $id, PDO::PARAM_INT);

        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        $result->bindParam(':client_name', $name, PDO::PARAM_STR);
        $result->bindParam(':client_email', $email, PDO::PARAM_STR);

        $result->bindParam(':client_phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':client_city', $city, PDO::PARAM_STR);
        $result->bindParam(':client_address', $address, PDO::PARAM_STR);
        $result->bindParam(':client_index', $index, PDO::PARAM_STR);
        $result->bindParam(':admin_comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':base_id', $base_id, PDO::PARAM_INT);
        $result->bindParam(':channel_id', $channel_id, PDO::PARAM_INT);
        $result->bindParam(':visit_param', $param, PDO::PARAM_STR);
        $result->bindParam(':ip', $ip, PDO::PARAM_STR);
        $result->execute();

        // Получить ID созданного заказа
        $result = $db->query(" SELECT order_id FROM ".PREFICS."orders WHERE order_date = $date ");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (isset($data) && !empty($data)) {

            $order_id = $data['order_id'];

            foreach($order_items as $item) {

                $number = 0;
                $var = null;
                $cast = 'custom'; // Основной продукт с которого начался заказ
                $sql = 'INSERT INTO '.PREFICS.'order_items (order_id, product_id, type_id, number, price, status, cast, product_name, split_var ) 
                        VALUES (:order_id, :product_id, :type_id, :number, :price, :status, :cast, :product_name, :split_var)';

                $product = Product::getProductById($item);
                if ($price == 1) $real_price = $product['price'];
                elseif ($price == 2) $real_price = 0;
                elseif ($price == 3) $real_price = 1;
                elseif ($price == 4) $real_price = floor($summ / count($order_items));
                else $real_price = $product['red_price'];

                $result = $db->prepare($sql);
                $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $result->bindParam(':product_id', $item, PDO::PARAM_INT);
                $result->bindParam(':type_id', $product['type_id'], PDO::PARAM_INT);
                $result->bindParam(':number', $number, PDO::PARAM_INT);
                $result->bindParam(':price', $real_price, PDO::PARAM_INT);
                $result->bindParam(':status', $status, PDO::PARAM_INT);
                $result->bindParam(':cast', $cast, PDO::PARAM_STR);
                $result->bindParam(':product_name', $product['product_name'], PDO::PARAM_STR);
                $result->bindParam(':split_var', $var, PDO::PARAM_INT);
                $res = $result->execute();
            }

            if ($res) return $order_id;
            return false;
        } return false;
    }


    // ДАННЫЕ ЗАКАЗА ПО ID ДЛЯ АДМИНА
    public static function getOrderToAdmin($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."orders WHERE order_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data;
        else return false;
    }


    /**
     * ДАННЫЕ ЗАКАЗА ПО order_date ДЛЯ ПОКУПАТЕЛЯ
     * @param $order_date
     * @param null $status
     * @param null $installment
     * @return bool|mixed
     */
    public static function getOrderData($order_date, $status = null, $installment = null)
    {
        $status = intval($status);
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."orders WHERE order_date = $order_date";
        if ($status != null && $installment == null) {
            $sql .= " AND status = $status";
        } elseif ($status == 0 && $installment == 1) {
            $sql .= " AND status IN (0, 5) ";
        }

        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


	// EMAIL в заказе по id заказа
    public static function getEmailByOrder($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT client_email FROM ".PREFICS."orders WHERE order_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data) && !empty($data)) return $data['client_email'];
        else return false;
    }



    // ДАННЫЕ ЗАКАЗА ПО order_id ДЛЯ ПОКУПАТЕЛЯ
    public static function getOrderDataByID($id, $status)
    {
        $status = intval($status);
        $sql = "SELECT * FROM " . PREFICS . "orders WHERE order_id = $id";
        if ($status != 100) {
            $sql .= " AND status = $status";
        } else {
            $sql .= " AND status IN (0, 5)";
        }

        $db = Db::getConnection();
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ЗАКАЗА
     * @param $id
     * @return bool|mixed
     */
    public static function getOrder($id)
    {
        $db = Db::getConnection();
        $result = $db->query('SELECT * FROM '.PREFICS."orders WHERE order_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

    /**
     * ПРОДУКТЫ В ЗАКАЗЕ
     * @param $order_id
     * @return array|bool
     */
    public static function getOrderItems($order_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."order_items WHERE order_id = $order_id ORDER BY order_item_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * @param $product_id
     * @param $user_id
     * @param null $user_email
     * @param null $user_ip
     * @return mixed
     */
    public static function getOrderByProductId2User($product_id, $user_id, $user_email = null, $user_ip = null) {
        $db = Db::getConnection();
        $clauses = ['oi.product_id = :product_id'];
        if ($user_id) {
            $clauses[] = 'u.user_id = :user_id';
        }
        if ($user_email) {
            $clauses[] = 'o.client_email = :user_email';
        }
        if ($user_ip) {
            $clauses[] = "o.ip = :user_ip";
        }
        $where = 'WHERE ' . implode(' AND ', $clauses);
        $sql = 'SELECT o.* FROM '.PREFICS.'orders AS o
                LEFT JOIN '.PREFICS.'order_items AS oi ON oi.order_id = o.order_id
                LEFT JOIN '.PREFICS."users AS u ON u.email = o.client_email $where";
        $result = $db->prepare($sql);

        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        if ($user_id) {
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        }
        if ($user_email) {
            $result->bindParam(':user_email', $user_email, PDO::PARAM_STR);
        }
        if ($user_ip) {
            $result->bindParam(':user_ip', $user_ip, PDO::PARAM_STR);
        }

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ПРОДУКТ К ЗАКАЗУ
     * @param $order_id
     * @param $product_id
     * @param $type_id
     * @param $number
     * @param $price
     * @param $cast
     * @param $product_name
     * @param $status
     * @return bool
     */
    public static function addOrderItem($order_id, $product_id, $type_id, $number, $price, $cast, $product_name, $status) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'order_items (order_id, product_id, type_id, number, price, status, cast, product_name) 
                VALUES (:order_id, :product_id, :type_id, :number, :price, :status, :cast, :product_name)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':type_id', $type_id, PDO::PARAM_INT);
        $result->bindParam(':number', $number, PDO::PARAM_INT);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':cast', $cast, PDO::PARAM_STR);
        $result->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    // ОБНОВИТЬ ЗАКАЗ ПОСЛЕ АПСЕЛЛА
    public static function UpdateOrderAfterUpsell($order_date, $prod_id, $price, $type_id, $product_name)
    {
        $db = Db::getConnection();
        // Получить ID заказа по дате
        $result = $db->query(" SELECT order_id FROM ".PREFICS."orders WHERE order_date = $order_date AND status != 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if(!$data) return false;

        $number = 0;
        $status = 0;
        $cast = 'upsell';

        // Записать продукт в items
        $sql = 'INSERT INTO '.PREFICS.'order_items (order_id, product_id, type_id, number, price, status, cast, product_name ) 
                VALUES (:order_id, :product_id, :type_id, :number, :price, :status, :cast, :product_name)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $data['order_id'], PDO::PARAM_INT);
        $result->bindParam(':product_id', $prod_id, PDO::PARAM_INT);
        $result->bindParam(':number', $number, PDO::PARAM_INT);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':type_id', $type_id, PDO::PARAM_INT);
        $result->bindParam(':cast', $cast, PDO::PARAM_STR);
        $result->bindParam(':product_name', $product_name, PDO::PARAM_STR);

        $data2 = $result->execute();

        if ($data2) {
            $order_id = $data['order_id'];
            $total = Order::getOrderTotalSum($order_id);
            $sql = 'UPDATE '.PREFICS."orders SET summ = :total WHERE order_id = $order_id";
            $result = $db->prepare($sql);
            $result->bindParam(':total', $total, PDO::PARAM_INT);
            $result = $result->execute();
            return $result;
        }
    }



    // ПОЛУЧИТЬ СПИСОК СПОСОБОВ ДОСТАВКИ
    public static function getDeliveryMethods($status = 1)
    {
        $db = Db::getConnection();
        if ($status == 1)$result = $db->query("SELECT * FROM ".PREFICS."ship_methods WHERE status = $status ORDER BY method_id ASC");
        else $result = $db->query("SELECT * FROM ".PREFICS."ship_methods ORDER BY method_id ASC");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['title'] = $row['title'];
            $data[$i]['ship_desc'] = $row['ship_desc'];
            $data[$i]['tax'] = $row['tax'];
            $data[$i]['method_id'] = $row['method_id'];
            $data[$i]['status'] = $row['status'];
            $i++;
        }
        if (isset($data)) return $data;
        else return false;
    }


    // ПОЛУЧИТЬ ИМЯ МЕТОДА ДОСТАВКИ ПО ЕГО ID
    public static function getDeliveryMethodName($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT title FROM ".PREFICS."ship_methods WHERE method_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data['title'];
        else return false;
    }



    // ОБНОВИТЬ ЗАКАЗ ПОСЛЕ РУЧНОЙ ОПЛАТЫ
    public static function UpdateOrderCustom($order, $payment, $payment_data = false)
    {
        $db = Db::getConnection();
        if ($payment_data) $sql = 'UPDATE '.PREFICS.'orders SET status = 2, payment_id = :payment, order_info = :order_info WHERE order_date = :date';
        else $sql = 'UPDATE '.PREFICS.'orders SET status = 2, payment_id = :payment WHERE order_date = :date';
        $result = $db->prepare($sql);
        if ($payment_data) $result->bindParam(':order_info', $payment_data, PDO::PARAM_STR);
        $result->bindParam(':payment', $payment, PDO::PARAM_INT);
        $result->bindParam(':date', $order, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОБНОВИТЬ СПОСОБ ДОСТАВКИ В ЗАКАЗЕ
    public static function UpdateOrderDeliveryMethod($order, $method)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET ship_method_id = :method WHERE order_date = :date';
        $result = $db->prepare($sql);
        $result->bindParam(':method', $method, PDO::PARAM_INT);
        $result->bindParam(':date', $order, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОБНОВИТЬ ЗАКАЗ ПОСЛЕ ПОДТВЕРЖДЕНИЯ ДОСТАВКИ
    public static function UpdateOrderDeliveryConfirm($order, $status)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET status = :status WHERE order_date = :date';
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':date', $order, PDO::PARAM_INT);
        return $result->execute();
    }


    // ИЗМЕНИТЬ ЗАКАЗ В АДМИНКЕ
    public static function updateOrderToAdmin($id, $name, $email, $phone, $city, $index, $address, $status, $comment, $admin_comment, $order_date, $payment_date)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."orders SET client_name = :client_name, client_email = :email, client_phone = :client_phone,
                client_city = :client_city, client_address = :client_address, client_index = :client_index,
                client_comment = :client_comment, admin_comment = :admin_comment, status = :status, order_date = :order_date";
        $sql .= ($payment_date !== null ? ', payment_date = :payment_date' : '') . " WHERE order_id = $id";

        $result = $db->prepare($sql);
        $result->bindParam(':client_name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':client_phone', $phone, PDO::PARAM_STR);

        $result->bindParam(':client_city', $city, PDO::PARAM_STR);
        $result->bindParam(':client_index', $index, PDO::PARAM_STR);
        $result->bindParam(':client_address', $address, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':client_comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':admin_comment', $admin_comment, PDO::PARAM_STR);
        $result->bindParam(':order_date', $order_date, PDO::PARAM_INT);

        if ($payment_date !== null) {
            $result->bindParam(':payment_date', $payment_date, PDO::PARAM_INT);
        }

        return $result->execute();
    }


    // ОБНОВИТЬ ЦЕНЫ ПРОДУКТОВ В ЗАКАЗЕ
    public static function updatePrice($order_item_id, $order_id, $price)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."order_items SET price = :price WHERE order_item_id = $order_item_id";
        $result = $db->prepare($sql);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result = $result->execute();

        if ($result) {
            $total = self::getOrderTotalSum($order_id);
            if ($total !== false) {
                $sql = 'UPDATE '.PREFICS."orders SET summ = :price WHERE order_id = $order_id";
                $result = $db->prepare($sql);
                $result->bindParam(':price', $price, PDO::PARAM_INT);
                $result = $result->execute();
            }
        }

        return $result;
    }

    // ОБНОВИТЬ ID ПРОДУКТА В ЗАКАЗЕ
    public static function updateProductId($order_item_id, $order_id, $prod_id, $order_prod_id)
    {
        $db = Db::getConnection();
		$result = $db->query(" SELECT * FROM ".PREFICS."products WHERE product_id = $prod_id LIMIT 1");
		$data = $result->fetch(PDO::FETCH_ASSOC);
		if (!empty($data)) {

			$sql = 'UPDATE '.PREFICS."order_items SET product_id = :product_id, product_name = :product_name WHERE order_item_id = $order_item_id";
			$result = $db->prepare($sql);
			$result->bindParam(':product_id', $prod_id, PDO::PARAM_INT);
			$result->bindParam(':product_name', $data['product_name'], PDO::PARAM_STR);
			$result = $result->execute();

			if ($result && $order_prod_id !== null) {
				$sql = 'UPDATE '.PREFICS."orders SET product_id = :product_id WHERE order_id = $order_id";
				$result = $db->prepare($sql);
				$result->bindParam(':product_id', $prod_id, PDO::PARAM_INT);
				$result = $result->execute();
			}

			return $result;
		} else return false;
    }

    // УДАЛИТЬ ПРОДУКТ ИЗ ЗАКАЗА
    public static function deleteOrderItem($id, $order_item)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(order_item_id) FROM ".PREFICS."order_items WHERE order_id = $id");
        $count = $result->fetch();
        $i = $count[0];

        if ($i > 1) {

            $sql = 'DELETE FROM '.PREFICS.'order_items WHERE order_item_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $order_item, PDO::PARAM_INT);
            $data2 = $result->execute();

            if ($data2) {
                $total = Order::getOrderTotalSum($id);
                $sql = 'UPDATE '.PREFICS."orders SET summ = :total WHERE order_id = $id";
                $result = $db->prepare($sql);
                $result->bindParam(':total', $total, PDO::PARAM_INT);
                return $result->execute();
            }

        } else return false;
    }
    
    
    
    // ЗАПИСАТЬ КОНВЕРСИЮ = СПЛИТ ТЕСТ
    public static function WriteConversion($product_id, $var, $order_id)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'split_tests (product_id, variant, order_id ) 
                VALUES (:product_id, :variant, :order_id)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':variant', $var, PDO::PARAM_INT);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * кол-во заказов
     * @param null $status
     * @param null $email
     * @param null $number
     * @param null $start
     * @param null $finish
     * @param null $paid
     * @param null $product_id
     * @return bool
     */
    public static function countOrders($status = null, $email = null, $number = null, $start = null,
        $finish = null, $paid = null, $product_id = null)
    {
        $clauses = [];
        if ($status !== null) {
            $statuses = ['ok' => 1, 'refund' => 9, 'no' => 0, 'check' => 2, 'confirm' => 7, 'inst' => 5];
            $clauses[] = 'o.status= ' . (isset($statuses[$status]) ? $statuses[$status] : $status);
        }
        if ($start != null) {
            $clauses[] = ($status == 1 ? 'o.payment_date' : 'o.order_date') . " >= $start";
        }
        if ($finish != null) {
            $clauses[] = ($status == 1 ? 'o.payment_date' : 'o.order_date') . " < $finish";
        }
        if ($paid == 1 || $paid == 2) {
            $clauses[] = $paid == 1 ? 'o.summ > 0' : 'o.summ = 0';
        }
        if ($number != null) {
            $clauses[] = "o.order_date = $number";
        }
        if ($email != null) {
            $clauses[] = "o.client_email LIKE '%$email%'";
        }
        if ($product_id != null) {
            $clauses[] = "oi.product_id = $product_id";
        }

        $where = !empty($clauses) ? ("WHERE " . implode(' AND ', $clauses)) : '';
        $sql = "SELECT COUNT(o.order_id) FROM ".PREFICS."orders AS o
                LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id $where";
        
        $db = Db::getConnection();
        $result = $db->query($sql);
        $count = $result->fetch();
    
        return $count[0] > 0 ? $count[0] : false;
    }
    
    
    // УДАЛИТЬ ЗАКАЗ
    public static function deleteOrder($id)
    {
        // + Удалить все items в заказе
        $db = Db::getConnection();
    
        $result = $db->query(" SELECT * FROM ".PREFICS."orders WHERE order_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)) if($data['installment_map_id'] != 0 && $data['status'] == 1) return false;
        
        $sql = 'DELETE FROM '.PREFICS.'orders WHERE order_id = :id; DELETE FROM '.PREFICS.'order_items WHERE order_id = :id;
        DELETE FROM '.PREFICS.'aff_transaction WHERE order_id = :id; DELETE FROM '.PREFICS.'author_transaction WHERE order_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    /**
     *  ПЛАТЁЖНЫЕ СИСТЕМЫ
     */
    
    // ПОЛУЧИТЬ СПИСОК ПЛАТЁЖНЫХ МОДУЛЕЙ ДЛЯ АДМИНКИ
    public static function getPaymentsForAdmin()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."payments ORDER BY sort ASC");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['payment_id'] = $row['payment_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['title'] = $row['title'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['sort'] = $row['sort'];
            $data[$i]['params'] = $row['params'];
            $i++;
        }
        if (isset($data)) return $data;
    }
    
    
    
    // ПОЛУЧИТЬ СПИСОК ПЛАТЁЖЕК ПРИ ЗАКАЗЕ
    public static function getPayments()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."payments WHERE status = 1 ORDER BY sort ASC");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['payment_id'] = $row['payment_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['title'] = $row['title'];
			$data[$i]['public_title'] = $row['public_title'];											 
            $data[$i]['status'] = $row['status'];
            $data[$i]['sort'] = $row['sort'];
            $data[$i]['params'] = $row['params'];
            $data[$i]['payment_desc'] = $row['payment_desc'];
            $i++;
        }
        if (isset($data)) return $data;
    }
    
    
    
    public static function getPaymentDataForAdmin($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."payments WHERE payment_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data;
    }
    
    
    public static function EditPayments($id, $title, $public_title, $sort, $status, $payment_desc, $params)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'payments SET title = :title, payment_desc = :payment_desc, status = :status, sort = :sort, params = :params, public_title = :public_title
                WHERE payment_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':payment_desc', $payment_desc, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':public_title', $public_title, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        return $result->execute();
    }
    
    // ПОЛУЧИТЬ НАСТРОЙКИ МОДУЛЯ РУЧНОЙ ОПЛАТЫ
    public static function getDataCustomModule($name = null)
    {
        $db = Db::getConnection();
        if ($name == null) $result = $db->query(" SELECT * FROM ".PREFICS."payments WHERE name = 'custom' ");
        else $result = $db->query(" SELECT * FROM ".PREFICS."payments WHERE name = 'company' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data;
        else return false;
    } 
    
    
    // ПОЛУЧИТЬ НАСТРОЙКИ Модуля ОПЛАТЫ
    public static function getPaymentSetting($name)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."payments WHERE name = '$name' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data;
        else return false;
    }
    
    
    
    
    /**
     *   ВОЗВРАТ ТОВАРА
     */
    
    
    // СМЕНА СТАТУСА заказа и товара в нём
    public static function ChangeStatus ($order, $item, $status = 9)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'order_items SET status = :status WHERE order_item_id = :item ; UPDATE '.PREFICS.'orders SET status = :status WHERE order_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':item', $item, PDO::PARAM_INT);
        $result->bindParam(':id', $order, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     *   РАСПРЕДЕЛЕНИЕ ПО КУРАТОРАМ
     */
    
     // НАЗНАЧАЕМ юзеру куратора по тренингу/разделу
     // тут пока все в кучу будет, позже раскидаю на функции и какие-то логические блоки
     public static function AssignUserToCurator($user_id, $training)
    {  
        /// ---begin-----   Тут идет распределение по секциям тренинга !!!! ------------------
        $training_id = $training['training_id'];
        $db = Db::getConnection();
        $result = $db->query("SELECT section_id, curator_id FROM ".PREFICS."training_curators_in_training where training_id = $training_id
        and section_id <> 0");
        $data = $result->fetchAll(PDO::FETCH_GROUP);
        $all_curators_user = Self::GetAllCuratorsToUser(intval($user_id));
        if (isset($data)) {
            // тут каждая итерация это кураторы раздела! 
            foreach($data as $key => $curators_tosection) {
                    $list_curators = [];
                    foreach($curators_tosection as $curator_id) {
                        if (in_array($curator_id[0], $all_curators_user)) {
                            User::WriteCuratorsToUser($user_id, intval($curator_id[0]), $training_id, $key);  
                            break;
                        } else {
                            // здесь нужно проверить кол-во юзеров у куратора и добавить
                            // в массив, потом сортируем по меньшему и записываем ему пользователя
                            $count_users = Self::GetCountUsersToCurator(intval($curator_id[0]));
                            if (intval($count_users)>0) {
                                $list_curators[$curator_id[0]] = intval($count_users);
                            } else {
                                User::WriteCuratorsToUser($user_id, intval($curator_id[0]), $training_id, $key);
                                $list_curators = [];
                                break;
                            }
                        }
                    } 
                    //Если кол-во кураторов в секции больше чем 1
                    // то сортируем по кол-ву пользователей и назначаем меньшего
                    if (count($list_curators)>1) {
                        asort($list_curators);
                        User::WriteCuratorsToUser($user_id, key($list_curators), $training_id, $key);
                    } elseif (count($list_curators)==1)  {
                        // тут просто пишем юзеру куратора 
                        User::WriteCuratorsToUser($user_id, key($list_curators), $training_id, $key);
                    } else {
                        continue;
                    }
            }

        }
        /// ---end-----   Тут идет распределение по секциям тренинга !!!! ------------------

        /// ---begin-----   Тут идет распределение по тренингу !!!! ------------------
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT curator_id FROM ".PREFICS."training_curators_in_training where training_id = $training_id
        and section_id = 0 and assing_to_users = 1");
        $data = $result->fetchAll();
        $all_curators_user = Self::GetAllCuratorsToUser(intval($user_id));
        if (isset($data)) {
            // тут каждая итерация это кураторы тренинга!
            $list_curators = []; 
            foreach($data as $curator_id) {
                if (in_array($curator_id[0], $all_curators_user)) {
                    User::WriteCuratorsToUser($user_id, intval($curator_id[0]), $training_id);
                    break;
                } else {
                    // здесь нужно проверить кол-во юзеров у куратора и добавить
                    // в массив, потом сортируем по меньшему и записываем ему пользователя
                    $count_users = Self::GetCountUsersToCurator(intval($curator_id[0]));
                    if (intval($count_users)>0) {
                        $list_curators[$curator_id[0]] = intval($count_users);
                    } else {
                        User::WriteCuratorsToUser($user_id, intval($curator_id[0]), $training_id);
                        break;  
                    }
                }
            }
            //Если кол-во кураторов в секции больше чем 1
            // то сортируем по кол-ву пользователей и назначаем меньшего
            if (count($list_curators)>1) {
                asort($list_curators);
                User::WriteCuratorsToUser($user_id, key($list_curators), $training_id);
            } elseif (count($list_curators)==1)  {
                // тут просто пишем юзеру куратора 
                User::WriteCuratorsToUser($user_id, key($list_curators), $training_id);
            } 
        }
    }
  
    /**
    * ПОЛУЧИТЬ ВСЕХ КУРАТОРОВ ПОЛЬЗОВАТЕЛЯ
    * @param int $user_id
    * @return array|bool
    */

    public static function GetAllCuratorsToUser($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT curator_id FROM ".PREFICS."training_curator_to_user where user_id = $user_id");
        $data = $result->fetchAll(PDO::FETCH_COLUMN);
        if (isset($data)) return $data;
        else return false;
    }

       /**
    * ПОЛУЧИТЬ КОЛ-ВО ПОЛЬЗОВАТЕЛЕЙ У КУРАТОРА
    * @param int $user_id
    * @return array|bool
    */

    public static function GetCountUsersToCurator($curator)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(DISTINCT user_id) as count_user FROM ".PREFICS."training_curator_to_user where curator_id = $curator");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) return $data['count_user'];
        else return false;
    }


    /**
     * ПОЛУЧИТЬ СТАТИСТИЧЕСКИЕ ДАННЫЕ ИЗ ЗАКАЗА
     * @param $order
     * @return array
     */
    public static function getStatisticsData($order) {
        $order_info = $order['order_info'] ? unserialize(base64_decode($order['order_info'])) : null;
        $statistics_data = [
            'userId_YM' => $order_info && isset($order_info['userId_YM']) ? $order_info['userId_YM'] : null,
            'userId_GA' => $order_info && isset($order_info['userId_GA']) ? $order_info['userId_GA'] : null,
            'roistat_visitor' => $order_info && isset($order_info['roistat_visitor']) ? $order_info['roistat_visitor'] : null,
            'userId_FB' => $order_info && isset($order_info['userId_FB']) ? $order_info['userId_FB'] : null,
            'utm' => $order['utm'] ? System::getUtmData($order['utm']) : null,
        ];

        return $statistics_data;
    }


    /**
     * @param $status: 0 - неоплчен, 1- оплачен, 2 - требует проверки,
     *                 5 - рассрочка, 7 - подтверждение доставки по емейл, 9 - возврат
     * @return mixed|string
     */
    public static function getStatusText($status) {
        $text = '';
        switch ($status) {
            case 0:
                $text = System::Lang('NOT_PAID');
                break;
            case 1:
                $text = System::Lang('PAID');
                break;
            case 2:
                $text = System::Lang('VERIFY');
                break;
            case 5:
                $text = 'Идут платежи по рассрочке';
                break;
            case 7:
                $text = 'Подтверждение доставки по емейл';
                break;
            case 9:
                $text = System::Lang('REFUND');
                break;
        }

        return $text;
    }


    /**
     * ЗАПИСЬ ПЛАТЁЖНЫХ ТРАНЗАКЦИЙ В ЛОГ
     * @param $order_date
     * @param $subs_id
     * @param $specify
     * @param $query
     * @param $payment_id
     * @return bool
     */
    public static function writePayLog($order_date, $subs_id, $specify, $query, $payment_id)
    {
        $db = Db::getConnection();
        $date = time();
        $sql = 'INSERT INTO '.PREFICS.'payment_log (transaction_date, order_date, subs_id, specify, query, payment_id ) 
                VALUES (:transaction_date, :order_date, :subs_id, :specify, :query, :payment_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':transaction_date', $date, PDO::PARAM_INT);
        $result->bindParam(':order_date', $order_date, PDO::PARAM_INT);
        $result->bindParam(':subs_id', $subs_id, PDO::PARAM_STR);
        $result->bindParam(':specify', $specify, PDO::PARAM_STR);
        $result->bindParam(':query', $query, PDO::PARAM_STR);
        $result->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'payment_log WHERE transaction_date < :date');
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }
}
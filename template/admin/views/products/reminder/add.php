<div class="uk-modal-dialog uk-modal-dialog-3">
    <div class="userbox modal-userbox-3">
        <form action="/admin/products/addreminder" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
            <input type="hidden" name="product_id" value="<?=$product['product_id'];?>">
            <input type="hidden" name="product_type" value="<?=$product['type_id'];?>">

            <div class="admin_top admin_top-flex">
                <div class="admin_top-inner">
                    <div>
                        <img src="/template/admin/images/icons/nastr-icon.svg" alt="">
                    </div>

                    <div>
                        <h3 class="traning-title mb-0">Добавить тексты уведомлений</h3>
                    </div>
                </div>

                <ul class="nav_button">
                    <li>
                        <input type="submit" name="save_letters" value="Сохранить" class="button save button-white font-bold">
                    </li>
                    <li class="nav_button__last">
                        <a class="button red-link uk-modal-close uk-close" href="#close">Закрыть</a>
                    </li>
                </ul>
            </div>
    
            <div class="tabs">
                <ul>
                    <li>Основное</li>
                    <li>Письма</li>
                    <li>SMS</li>
                </ul>
    
                <div class="admin_form">
                    <div>
                        <div class="row-line">
                            <div class="col-1-1">
                                <h4 class="h4-border">Основное</h4>
                    
                                <div class="width-100"><label>Статус:</label>
                                    <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1"><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0" checked><span>Откл</span></label>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="row-line">
                            <div class="col-1-1">
                                <h4 class="h4-border">Письмо о выписанном счёте</h4>
                                
                                <div class="menu-apsell mb-20">
                                    <ul>
                                        <li>Напоминание 1</li>
                                        <li>Напоминание 2</li>
                                        <li>Напоминание 3</li>
                                    </ul>
                        
                                    <div>
                                        <div>
                                            <div class="width-100"><label>Отправлять:</label>
                                                <span class="custom-radio-wrap">
                                                    <label class="custom-radio"><input name="remind_letter1[status]" type="radio" value="1"><span>Вкл</span></label>
                                                    <label class="custom-radio"><input name="remind_letter1[status]" type="radio" value="0" checked><span>Откл</span></label>
                                                </span>
                                            </div>
                                
                                            <div class="width-100"><label>Отправить через:</label>
                                                <div class="time-wrap">
                                                    <input class="time-input" size="4" type="text" name="remind_letter1[time]">
                                                    <div class="time-value">мин.</div>
                                                </div>
                                            </div>
                                
                                            <div class="width-100"><label>Тема письма:</label>
                                                <input size="35" type="text" name="remind_letter1[subject]">
                                            </div>
                                
                                            <div class="width-100"><label>Текст письма:</label><br />
                                                <textarea class="editor" name="remind_letter1[text]" rows="5" cols="55"></textarea>
                                            </div>
                                
                                            <div class="width-100">
                                                <p class="small">Переменные для подстановки:</p>
                                                <p class="small">
                                                    [NAME] - имя клиента<br/>
                                                    [ORDER] - номер заказа<br/>
                                                    [LINK] - ссылка на неоплаченный заказ<br/>
                                                    [PINCODE] - ключ активации (пин код) для продукта<br/>
                                                </p>
                                            </div>
                                        </div>
                            
                                        <div>
                                            <div class="width-100"><label>Отправлять:</label>
                                                <span class="custom-radio-wrap">
                                                    <label class="custom-radio"><input name="remind_letter2[status]" type="radio" value="1"><span>Вкл</span></label>
                                                    <label class="custom-radio"><input name="remind_letter2[status]" type="radio" value="0" checked><span>Откл</span></label>
                                                </span>
                                            </div>
                                
                                            <div class="width-100"><label>Отправить через:</label>
                                                <div class="time-wrap">
                                                    <input class="time-input" size="4" type="text" name="remind_letter2[time]">
                                                    <div class="time-value">мин.</div>
                                                </div>
                                            </div>
                                
                                            <div class="width-100"><label>Тема письма:</label>
                                                <input size="35" type="text" name="remind_letter2[subject]">
                                            </div>
                                
                                            <div class="width-100"><label>Текст письма:</label><br />
                                                <textarea class="editor" name="remind_letter2[text]" rows="5" cols="55"></textarea>
                                            </div>
                                
                                            <div class="width-100">
                                                <p class="small">Переменные для подстановки:</p>
                                                <p class="small">
                                                    [NAME] - имя клиента<br/>
                                                    [ORDER] - номер заказа<br/>
                                                    [LINK] - ссылка на неоплаченный заказ<br/>
                                                    [PINCODE] - ключ активации (пин код) для продукта<br/>
                                                </p>
                                            </div>
                                        </div>
                            
                                        <div>
                                            <div class="width-100"><label>Отправлять:</label>
                                                <span class="custom-radio-wrap">
                                                    <label class="custom-radio"><input name="remind_letter3[status]" type="radio" value="1"><span>Вкл</span></label>
                                                    <label class="custom-radio"><input name="remind_letter3[status]" type="radio" value="0" checked><span>Откл</span></label>
                                                </span>
                                            </div>
                                
                                            <div class="width-100"><label>Отправить через:</label>
                                                <div class="time-wrap">
                                                    <input class="time-input" size="4" type="text" name="remind_letter3[time]">
                                                    <div class="time-value">мин.</div>
                                                </div>
                                            </div>
                                
                                            <div class="width-100"><label>Тема письма:</label>
                                                <input size="35" type="text" name="remind_letter3[subject]">
                                            </div>
                                
                                            <div class="width-100"><label>Текст письма:</label><br />
                                                <textarea class="editor" name="remind_letter3[text]" rows="5" cols="55"></textarea>
                                            </div>
                                
                                            <div class="width-100">
                                                <p class="small">Переменные для подстановки:</p>
                                                <p class="small">
                                                    [NAME] - имя клиента<br/>
                                                    [ORDER] - номер заказа<br/>
                                                    [LINK] - ссылка на неоплаченный заказ<br/>
                                                    [PINCODE] - ключ активации (пин код) для продукта<br/>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div>
                        <div class="row-line">
                            <div class="col-1-1">
                                <h4>SMS о неоплаченном заказе</h4>
                                
                                <div class="menu-apsell mb-20">
                                    <ul>
                                        <li>SMS 1</li>
                                        <li>SMS 2</li>
                                    </ul>
        
                                    <div>
                                        <div>
                                            <div class="width-100"><label>Отправлять SMS:</label>
                                                <span class="custom-radio-wrap">
                                                    <label class="custom-radio"><input name="remind_sms1[status]" type="radio" value="1"><span>Вкл</span></label>
                                                    <label class="custom-radio"><input name="remind_sms1[status]" type="radio" value="0" checked><span>Откл</span></label>
                                                </span>
                                            </div>
                                
                                            <div class="width-100"><label>Отправить sms через:</label>
                                                <div class="time-wrap">
                                                    <input class="time-input" size="4" type="text" name="remind_sms1[time]">
                                                    <div class="time-value">мин.</div>
                                                </div>
                                            </div>
                                
                                            <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                                <textarea name="remind_sms1[text]"></textarea><br />
                                            </div>
                    
                                            <div class="width-100">
                                                <p class="small">Переменные для подстановки:</p>
                                                <p>
                                                    [NAME] - имя клиента<br/>
                                                    [LINK] - ссылка на оплату заказа
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="width-100"><label>Отправлять SMS:</label>
                                                <span class="custom-radio-wrap">
                                                    <label class="custom-radio"><input name="remind_sms2[status]" type="radio" value="1"><span>Вкл</span></label>
                                                    <label class="custom-radio"><input name="remind_sms2[status]" type="radio" value="0" checked><span>Откл</span></label>
                                                </span>
                                            </div>
                        
                                            <div class="width-100"><label>Отправить sms через:</label>
                                                <div class="time-wrap">
                                                    <input class="time-input" size="4" type="text" name="remind_sms2[time]">
                                                    <div class="time-value">мин.</div>
                                                </div>
                                            </div>
                        
                                            <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                                <textarea name="remind_sms2[text]"></textarea>
                                            </div>
                        
                                            <div class="width-100">
                                                <p class="small">Переменные для подстановки:</p>
                                                <p>
                                                    [NAME] - имя клиента<br/>
                                                    [LINK] - ссылка на оплату заказа
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
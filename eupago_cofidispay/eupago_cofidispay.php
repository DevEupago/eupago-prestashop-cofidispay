<?php
/**
 *  2007-2015 PrestaShop
 *
 *  @author    Eupago <suporte@eupago.pt>
 *  @copyright 2013-2022 Eupago, instituição de pagamento LDA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Eupago_cofidispay extends PaymentModule
{
    protected $config_form = false;

    private $_html = '';
    private $_postErrors = [];
    public $chave_api;
    public $cart;

    public function __construct()
    {
        $this->name = 'eupago_cofidispay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'Eupago';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = ['min' => '1.0', 'max' => _PS_VERSION_];

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->currencies = true;

        parent::__construct();

        $this->displayName = $this->l('Eupago - Cofidis Pay');
        $this->description = $this->l('Allow your customers to pay your order with Cofidis Pay');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Eupago - Cofidis Pay module?');

        $this->limited_currencies = ['EUR'];

        $this->context->link->getModuleLink($this->name, 'display');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');

            return false;
        }
        $this->createOrderState();

        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('paymentOptions') &&
        $this->registerHook('paymentReturn');
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitEupago_cofidispayModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $this->renderForm() . $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEupago_cofidispayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 7,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('This key is provided by Eupago if you don´t have it please contact us - www.eupago.pt'),
                        'name' => 'EUPAGO_COFIDISPAY_CHAVE_API',
                        'label' => $this->l('Api key'),
                    ],
                    [
                        'col' => 7,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-refresh"></i>',
                        'desc' => $this->l('When the payment is completed the client will be redirect to this url. If you dont know what is it please leave blank'),
                        'name' => 'EUPAGO_COFIDISPAY_RETURN_URL',
                        'label' => $this->l('Return url'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'EUPAGO_COFIDISPAY_CHAVE_API' => Configuration::get('EUPAGO_COFIDISPAY_CHAVE_API', null),
            'EUPAGO_COFIDISPAY_RETURN_URL' => Configuration::get('EUPAGO_COFIDISPAY_RETURN_URL', null),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * This method is used to render the payment button in version 1.7,
     * Take care if the button should be displayed or not.
     *
     * @param object $params
     */
    public function hookPaymentOptions($params)
    {
        $payment_option = [];

        if (!$this->active) {
            return;
        }

        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int) $currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false) {
            return false;
        }

        $this->smarty->assign('module_dir', $this->_path);
        $this->smarty->assign('cart', $params['cart']);

        $cart = $this->context->cart;
        $total_wt = $cart->getOrderTotal(true, Cart::BOTH);

        if ($total_wt >= 60 && $total_wt <= 1000) {
            $newOption = new PaymentOption();

            $newOption->setModuleName($this->name)
                ->setCallToActionText($this->l('Cofidis Pay'))
                ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/cofidispay.png'))
                ->setAdditionalInformation('Até ' . $this->get_numero_prestacoes($total_wt) . 'x sem juros <br/><br/>Será redirecionado para uma página segura a fim de efetuar o pagamento.<br/>O pagamento das prestações será efetuado no cartão de débito ou crédito do cliente através de solução de pagamento assente em contrato de factoring entre a Cofidis e o Comerciante. Informe-se na <a href="https://www.cofidis.pt/cofidispay" target="_blank">Cofidis</a>.')
                ->setForm($this->generateCofidisForm());

            $payment_option[] = $newOption;

            return $payment_option;
        }
    }

    protected function generateCofidisForm()
    {
        $this->context->smarty->assign([
            'action' => $this->context->link->getModuleLink($this->name, 'redirect', [], true),
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/front/payment_form.tpl');
    }

    protected function get_numero_prestacoes($order_total)
    {
        switch (true) {
            case $order_total >= 240:
                $number = 12;

                break;

            case $order_total >= 220:
                $number = 11;

                break;

            case $order_total >= 200:
                $number = 10;

                break;

            case $order_total >= 180:
                $number = 9;

                break;

            case $order_total >= 160:
                $number = 8;

                break;

            case $order_total >= 140:
                $number = 7;

                break;

            case $order_total >= 120:
                $number = 6;

                break;

            case $order_total >= 100:
                $number = 5;

                break;

            case $order_total >= 80:
                $number = 4;

                break;

            default:
                $number = 3;

                break;
        }

        return $number;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    /**
     * This hook is used to display the order confirmation page.
     *
     * @param object $params
     */
    public function hookPaymentReturn($params)
    {
        if ($this->active == false) {
            return;
        }

        $order = (_PS_VERSION_ >= '1.7' ? $params['order'] : $params['objOrder']);
        $eupago_status = Tools::getValue('status_eupago');
        $order_id = Tools::getValue('id_order');

        $state = $params['order']->getCurrentState();

        if ($eupago_status == 'impossivel') {
            $history = new OrderHistory();
            // $history->id_order = (int)$order->id;
            $history->id_order = $order_id;
            $history->changeIdOrderState((int) Configuration::get('PS_OS_ERROR'), $order_id);
        }

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR') && $eupago_status != 'impossivel') {
            $this->smarty->assign('status', 'ok');
        } else {
            $this->smarty->assign('status', 'Nok');
        }

        $this->smarty->assign([
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            // 'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
            'shop_name' => $this->context->shop->name,
            'total' => Tools::displayPrice($order->total_paid, null, false),
            'state' => $state,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }

    /**
     * Rest call and generate reference
     *
     * @param object $order
     */
    public function restCall($cart, $customer, $order_id, $cofidispay_vat_number)
    {
        //     VAI BUSCAR AS VARIAVEIS CONFIGURADAS NO BACKOFFICE
        $chave_api = Configuration::get('EUPAGO_COFIDISPAY_CHAVE_API');
        $url_retorno = Configuration::get('EUPAGO_COFIDISPAY_RETURN_URL');

        // VAI BUSCAR ORDER ID ????
        $id = $order_id;

        // DADOS DO CLIENTE
        $email = $customer->email;
        $nome = $customer->firstname . ' ' . $customer->lastname;
        $lang = $this->context->language->iso_code;

        // VAI BUSCAR COMENTARIO AO CONTROICOMENTARIO
        $comentario = $this->constroiComentarioParaFormulario($cart);

        // PREPARA O URL DE RETORNO
        if ($url_retorno == '') {
            $url_retorno = $this->context->link->getPageLink('order-confirmation', null, null, ['id_cart' => (int) $cart->id, 'key' => $customer->secure_key, 'id_module' => $this->id, 'id_order' => $order_id]);
        }

        // PREPARA O URL DA CHAMADA
        $demo = explode('-', $chave_api);

        if ($demo['0'] == 'demo') {
            $url_curl = 'https://sandbox.eupago.pt/api/v1.02/cofidis/create';
        } else {
            $url_curl = 'https://clientes.eupago.pt/api/v1.02/cofidis/create';
        }

        // CHAMADA REST
        $value = $cart->getOrderTotal();
        $produtos = $cart->getProducts();
        // $lang_upper=strtoupper($lang);

        // MORADA DA FATURA
        $user_address = new Address((int) $cart->id_address_invoice);

        $data = [
            'payment' => [
              'identifier' => (string) $id,
              'amount' => [
                'value' => $value,
                'currency' => 'EUR',
              ],
              'successUrl' => $url_retorno,
              'failUrl' => $url_retorno,
            ],
            'customer' => [
              'notify' => false,
              'email' => $email,
              'name' => $nome,
              'vatNumber' => $cofidispay_vat_number,
              'billingAddress' => [
                'address' => $user_address->address1 . ' ' . $user_address->address2,
                'zipCode' => (string) $user_address->postcode,
                'city' => $user_address->city,
              ],
            ],
            'items' => [],
          ];

        foreach ($produtos as $item) {
            $data['items'][] = [
                'reference' => $item['unique_id'],
                'price' => $item['total_wt'],
                'quantity' => $item['cart_quantity'],
                'tax' => $item['rate'],
                'discount' => 0,
                'description' => $item['name'],
            ];
        }

        $data = Tools::jsonEncode($data, JSON_UNESCAPED_UNICODE);

        $resposta = $this->curlRequest($url_curl, $data, $chave_api);

        $result = Tools::jsonDecode($resposta);

        // echo '<pre>';print_r($data);print_r($result);die();
        if ($result->transactionStatus == 'Success') {
            $this->saveResults($result, $order_id, $value);
        }

        return $result;
    }

    /**
     * Call cURL
     *
     * @param object $url
     * @param string $post
     * @param int $retries
     */
    private function curlRequest($url, $data, $chave_api)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            "Authorization: ApiKey $chave_api",
            'Content-Type: application/json',
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        } else {
            return $response;
        }
    }

    /**
     * Save result from Eupago server in DB
     *
     * @param int order_id
     *
     * @object result
     */
    public function saveResults($result, $order_id, $value)
    {
        Db::getInstance()->insert('eupago_cofidispay', [
            'id_eupago_cofidispay' => '',
            'order_id' => $order_id,
            'valor' => $value,
            'referencia' => $result->reference,
            'token' => $result->transactionID,
            'estadoRef' => 'pendente',
        ]);
    }

    /*
     * UPDATE EUPAGO_COFIDISPAY estado
     */
    public function updateStatus_DB($orderId)
    {
        Db::getInstance()->update(
            'eupago_cofidispay',
            [
            'estadoRef' => 'pago', ],
            'order_id = ' . $orderId
        );
    }

    /*
     * GET order_id in eupago DB
     */
    public function getOrderId_DB($referencia)
    {
        $sql = 'SELECT order_id FROM ' . _DB_PREFIX_ . 'eupago_cofidispay where referencia = ' . $referencia;

        return (int) Db::getInstance()->getValue($sql);
    }

    /*
     * GET order validate and update total_paid_real in Orders DB by order and paid value
     */
    public function updateValidateOrder($order_id, $valor)
    {
        $query = 'UPDATE `' . _DB_PREFIX_ . 'orders` SET total_paid_real=' . $valor . ', valid=1 WHERE id_order = ' . $order_id;
        Db::getInstance()->Execute($query);
    }

    /**
     * Buil cart information to display in Eupago payment page
     */
    public function constroiComentarioParaFormulario($cart)
    {
        $products = $cart->getProducts();
        $total = $cart->getordertotal();
        $total_produtos = 0;
        $comentario = '<ul style=\'margin:0; padding:0; font-size:0.75em; color:#333; \'>';

        foreach ($products as $product) {
            $total_produtos += $product['total_wt'];
            $comentario .= '<li style=\'list-style: none;\'><span style=\'margin:0; font-size:9px; margin-bottom:5px; padding:0;\' class=\'large-7 columns left\'>' . $product['name'] . '</span><span style=\'margin:0; padding:0; text-align:center;\' class=\'large-2 columns\'>x ' . $product['quantity'] . '</span><span style=\'margin:0; padding:0; text-align:right\' class=\'large-3 columns right\'>' . $product['total_wt'] . ' €</span></li>';
        }
        $envio_e_taxas = ($total - $total_produtos);
        $comentario .= '<li style=\'list-style: none; padding-top: 5px; border-top: 1px solid #ddd; display: inline-block; font-size:9px; width: 100%;\'><span style=\'margin:0; padding:0;\' class=\'large-7 columns left\'>Envio e taxas:</span><span style=\'margin:0; padding:0; text-align:center;\' class=\'large-2 columns\'></span><span style=\'margin:0; padding:0; text-align:right\' class=\'large-3 columns right\'>' . $envio_e_taxas . ' €</span></li></ul>';

        return $comentario;
    }

    /**
     * Create a new order state
     */
    public function createOrderState()
    {
        if (!Configuration::get('EUPAGO_A_AGUARDAR_PAGAMENTO_COFIDISPAY')) {
            $order_state = new OrderState();
            $order_state->name = [];

            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'pt') {
                    $order_state->name[$language['id_lang']] = 'Eupago - A aguardar pagamento por Cofidis Pay';
                } else {
                    $order_state->name[$language['id_lang']] = 'Eupago - Waiting Cofidis Pay payment confirmation';
                }
            }

            $order_state->send_email = false;
            // $OrderState->template = array_fill(0,10,"SB24"); // ver melhor isto passar para order_conf
            $order_state->color = '#ffdb1c';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();

            Configuration::updateValue('EUPAGO_A_AGUARDAR_PAGAMENTO_COFIDISPAY', (int) $order_state->id);
        }
    }

    /**
     * Function for callback handling
     *
     * @param string referencia
     * @param int valor
     * @param string chave
     */
    public function callback($referencia, $valor, $chave, $identificador)
    {
        // global $link;
        $chave_api = Configuration::get('EUPAGO_COFIDISPAY_CHAVE_API');
        $context = Context::getContext();
        $context->link = new Link();

        if ($chave == $chave_api) {
            $valor = str_replace(',', '.', $valor);
            $order_byReference = $this->getOrderByReference($referencia, $valor);

            if ($order_byReference[0]['order_id'] != $identificador) {
                return 'O identificador e a referencia não correspondem para esta encomenda';
            }

            if ($order_byReference[0]['estadoRef'] == 'pago') {
                return 'Referencia Já paga';
            }
            $orderId = $identificador;

            if (!empty($orderId)) {
                $new_history = new OrderHistory();
                $new_history->id_order = $orderId;
                $new_history->changeIdOrderState((int) 2, (int) $orderId);

                $lang = $this->context->language->iso_code;
                $subject = ($lang == 'pt') ? 'Pagamento bem sucedido' : 'Successful payment';
                // procurar o email do cliente para enviar lhe a notificação de pagamento bem sucedido
                $sql = 'SELECT ' . _DB_PREFIX_ . 'customer.email, ' . _DB_PREFIX_ . 'orders.id_lang,' .
                _DB_PREFIX_ . 'orders.reference FROM ' .
                _DB_PREFIX_ . 'orders,' . _DB_PREFIX_ . 'customer WHERE ' .
                _DB_PREFIX_ . 'orders.id_order=' . (int) $orderId . ' and ' .
                _DB_PREFIX_ . 'orders.id_customer = ' . pSQL(_DB_PREFIX_ . 'customer.id_customer');
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                Mail::Send(
                    (int) $result[0]['id_lang'], // defaut language id
                    'payment', // email template file to be use
                    $subject, // email subject
                    [
                        'message' => $subject,
                        '{firstname}' => $this->context->customer->firstname,
                        '{lastname}' => $this->context->customer->lastname,
                        '{order_name}' => $result[0]['reference'],
                    ],
                    $result[0]['email'], // receiver email address
                    null, // receiver name
                    null, // from email address
                    null// from name
                );

                echo 'Atualizada para paga';
                $this->updateStatus_DB($orderId);
                $this->updateValidateOrder($orderId, $valor);
                $new_history->addWithemail(true, null, $context);

                return 'Atualizada para paga'; // atualizada para paga
            } else {
                return 'Referencia não encontrada'; // Já paga
            }
        } else {
            return 'Chave de API inválida'; // Chave inválida
        }
    }

    /*
     * GET order in eupago DB by reference
     */
    public function getOrderByReference($referencia, $valor = null)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'eupago_cofidis where referencia = ' . $referencia . ' and valor = ' . $valor;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}

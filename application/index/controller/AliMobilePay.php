<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/25
 * Time: 14:56
 */
namespace Portal\Controller;
use Common\Component\FilterComponent;
use Portal\Service\LogPaycallbacksService;
use Portal\Service\GuozhanOrderService;
use Portal\Model\Pengwifi\Guozhan\OrderModel;
use Portal\Service\TokenService;
use Portal\Service\UserService;
use Portal\Service\SetMotoRadiusService;
use Common\Model\Radius\RadcheckModel;

/*
 * 购买上网卡的手机页面支付宝接口
 */
class AliMobilePayController extends CommonController{
    protected $_order_model=null;
    protected $_order_service=null;
    protected $_token_service = null;
    protected $_Set_MotoRadius_service=null;
    protected $_RadcheckModel=null;
    protected $_log_pay_callbacks = null;
    protected function afterInit() {
        parent::afterInit();
        vendor('AliMobilePay.Corefunction');
        vendor('AliMobilePay.Rsafunction');
        vendor('AliMobilePay.Notify');
        vendor('AliMobilePay.Submit');
        $this->_order_model= new OrderModel();
        $this->_order_service= new GuozhanOrderService();
        $this->_log_pay_callbacks = new LogPaycallbacksService();
        $this->_service = new UserService();
        $this->_token_service = new TokenService();
        $this->_RadcheckModel = new RadcheckModel();
        $this->_Set_MotoRadius_service = new SetMotoRadiusService();
    }
    /**
     * 执行新增订单
     */
    protected function _post(){
        if(isset($this->params['name']) && ($this->params['name']=="notify_url")){
            $this->notify_url('notify_url');
            die;
        }
        $this->insert_order();
    }
    protected function _get(){
        /*
         *根据配置文件里的路由规则:
         *':'.$var_controller.'/[:name]/[:action]'=>     ':1/_index?',   //匹配控制器后紧跟字符串,表示name
         * 例如：http://portal_v2.com/portal.php/Payment/Return.html
         * $notify_url会返回Return
         */
        $notify_url = isset($this->params['name']) ? FilterComponent::getString($this->params['name']) : 'Unknown';
        switch($notify_url){
            case 'return_url':
                $this->return_url($notify_url);
                break;

            default:
                $this->_log_pay_callbacks->update(array('request_from'=>'Unknown'), false);
                exit('Wrong request url');
        }
    }
    //服务器异步通知页面方法
    private function notify_url($notify_url){
        $alipay_config = C('ALIMOBILEPAY_CONFIG');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            //商户订单号
            $order_sn = $this->params['out_trade_no'];
            //支付宝交易号
            //$trade_no = $this->params['trade_no'];
            //交易状态
            $trade_status = $this->params['trade_status'];
            $this->_log_pay_callbacks->update(array('request_from'=>$notify_url, 'order_sn'=>$order_sn, 'response_status'=>$trade_status), false);
            if (in_array($trade_status,array('TRADE_SUCCESS','TRADE_FINISHED'))) {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                if(!$this->checkorderstatus($order_sn)){
                    $result=$this->orderhandle($order_sn);
                    if($result==true){
                        echo "success";
                    }else{
                        echo "fail";
                    }
                }
            }else{
                echo "fail";
            }
        }else {
            //验证失败
            echo "fail";
        }
    }
    //页面跳转同步通知
    private function return_url($notify_url){
        $alipay_config=C('ALIMOBILEPAY_CONFIG');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            //商户订单号
            $order_sn = $this->params['out_trade_no'];
            //支付宝交易号
            //$trade_no = $this->params['trade_no'];
            //交易状态
            $trade_status = $this->params['trade_status'];
            $this->_log_pay_callbacks->update(array('request_from'=>$notify_url, 'order_sn'=>$order_sn, 'response_status'=>$trade_status), false);
            if (in_array($trade_status,array('TRADE_SUCCESS','TRADE_FINISHED'))) {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                if(!$this->checkorderstatus($order_sn)){
                    $result=$this->orderhandle($order_sn);
                    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
                    if($result==true){
                        header("Location:".C('ALIMOBILEPAY_CONFIG.successpage'));//跳转到配置项中配置的支付成功页面；
                    }else{
                        header("Location:".C('ALIMOBILEPAY_CONFIG.errorpage'));//跳转到配置项中配置的支付失败页面；
                    }
                }
            }else {
                header("Location:".C('ALIMOBILEPAY_CONFIG.errorpage'));//跳转到配置项中配置的支付失败页面；
            }
        }else {
            //支付宝页面“返回商户”按钮的链接,商品页面
            header("Location:".C('ALIMOBILEPAY_CONFIG.product_url'));
        }
    }
    //在线交易订单支付处理函数
    //函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
    //返回值：如果订单已经成功支付，返回true，否则返回false；
    private function checkorderstatus($order_sn){
        $status=$this->_order_model->where("order_sn='$order_sn'")->getField('order_status');
        if($status == OrderModel::ORDER_STATUS_PAYED){
            return true;
        }else{
            return false;
        }
    }
    //处理订单函数
    //更新订单状态，写入订单支付后返回的数据
    private function orderhandle($order_sn){
        try{
            //开启事务
            $this->_order_model->startTrans();
            $data['order_status']=OrderModel::ORDER_STATUS_PAYED;
            $affected_row=$this->_order_model->where("order_sn='$order_sn'")->save($data);
            $find=$this->_order_model->where("order_sn='$order_sn'")->field('location_id,goods_id,mobile,goods_number')->find();
            //根据goods_id查找card_name对应的上网时长
            $goods_model=M('goods');
            $card_model=M('card');
            $card_name=$goods_model->where("id={$find['goods_id']}")->getField('card_name');
            $duration=$card_model->where("location_id={$find['location_id']} and card_name='$card_name'")->order('id desc')->getField('duration');
            $incre_time=($find['goods_number']) * $duration;
            $user_model=M('user');
            $mobile=$find['mobile'];
            $user_info=$user_model->where("user_name='{$mobile}'")->field('id,end_time')->find();
            $affected_row2=$user_model->where("user_name='{$mobile}'")->setInc('usable_time',$incre_time);
            //如果end_time 大于当前的时间戳就累计，否则就更新：使用当前时间戳 加上 $incre_time
            if($user_info['end_time'] >= time()){
                $user_model->where("user_name='{$mobile}'")->setInc('end_time',$incre_time);
            }else{
                $update_data['end_time']=time()+$incre_time;
                $user_model->where("user_name='{$mobile}'")->save($update_data);
            }
            if(empty($affected_row)){
                $this->_log_pay_callbacks->setException(L('ERROR_FAILED_UPDATE_ORDER'), $this->_log_pay_callbacks->getException('code'));
                throw new \Exception();
            }
            if(empty($affected_row2)){
                $this->_log_pay_callbacks->setException(L('ERROR_FAILED_UPDATE_USABLETIME'), $this->_log_pay_callbacks->getException('code'));
                throw new \Exception();
            }
            //提交更新
            if($affected_row && $affected_row2) {
                $this->_order_model->commit();
                return true;
            }
        }catch(\Exception $e){
            $this->_order_model->rollback();
            return false;
        }
    }

    private function insert_order(){
        $gw_id = isset($this->params['gw_id']) ? FilterComponent::get($this->params['gw_id']) : '';
        if (empty($gw_id)) {
            exit('400_EMPTY_GWID');
        }
        $router=M('router');
        $location_id=$router->where("gw_id='$gw_id'")->getField('supplier_location_id');
        $goods_number = isset($this->params['goods_number']) ? FilterComponent::get($this->params['goods_number'],'int') : '';
        if (empty($goods_number)) {
            exit('400_EMPTY_GOODSNUMBER');
        }
        $mobile = isset($this->params['mobile']) ? FilterComponent::get($this->params['mobile']) : '';
        if (!preg_match('/^1[0-9]{10}$/',$mobile)) {
            exit('400_ERROR_MOBILE');
        }
        $user=M('user');
        //查询充值号码是否存在
        $user_name=$user->where("user_name='$mobile'")->getField('user_name');
        if(!$user_name){
            exit('400_EMPTY_USERNAME');
        }
        $goods_id = isset($this->params['goods_id']) ? FilterComponent::get($this->params['goods_id'],'int') : '';
        if (empty($goods_id)) {
            exit('400_EMPTY_GOODSID');
        }

        $goods=M('goods');
        $unit_price=$goods->where("id=$goods_id")->getField('unit_price');
        $this->params['WIDtotal_fee']=$unit_price * $goods_number;

        $data['location_id']=$location_id;
        $data['mobile']=$mobile;
        $data['goods_id']=$goods_id;
        $data['goods_type']=1;//1代表充值卡
        $data['goods_number']=$goods_number;
        $data['total_price']=$this->params['WIDtotal_fee'];
        $data['pay_type']=OrderModel::PAY_TYPE_ALIPAY;//支付宝
        //执行添加操作
        $insert_id=$this->_order_service->update($data,false);
//        var_dump($this->_order_service->getError());
//        var_dump($this->_order_service->model->getError());
//        var_dump($this->_order_service->model->getlastsql());die;
        if($insert_id){
            $this->params['WIDout_trade_no']=$this->_order_model->where("id=$insert_id")->getField('order_sn');
            /**************************请求参数**************************/
            //支付类型
            $payment_type = "1";
            //必填，不能修改

            //商户订单号
            $out_trade_no = $this->params['WIDout_trade_no'];
            //商户网站订单系统中唯一订单号，必填

            $this->params['WIDsubject']='pengwifi_card';
            //订单名称
            $subject = $this->params['WIDsubject'];
            //必填

            //付款金额
            $total_fee = $this->params['WIDtotal_fee'];
            //必填

            //$this->params['WIDshow_url']=trim(C('ALIMOBILEPAY_CONFIG.product_url'));
            $this->params['WIDshow_url']=$_SERVER['HTTP_REFERER'];
            //商品展示地址
            $show_url = $this->params['WIDshow_url'];
            //必填，需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

            //订单描述
            $body = $this->params['WIDbody'];
            //选填

            //超时时间
            $it_b_pay = $this->params['WIDit_b_pay'];
            //选填

            //钱包token
            $extern_token = $this->params['WIDextern_token'];
            //选填

            /************************************************************/
            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "alipay.wap.create.direct.pay.by.user",
                "partner" => trim(C('ALIMOBILEPAY_CONFIG.partner')),
                "seller_id" => trim(C('ALIMOBILEPAY_CONFIG.seller_id')),
                "payment_type"  => $payment_type,
                "notify_url"    => trim(C('ALIMOBILEPAY_CONFIG.notify_url')),
                "return_url"    => trim(C('ALIMOBILEPAY_CONFIG.return_url')),
                "out_trade_no"  => $out_trade_no,
                "subject"   => $subject,
                "total_fee" => $total_fee,
                "show_url"  => $show_url,
                "body"  => $body,
                "it_b_pay"  => $it_b_pay,
                "extern_token"  => $extern_token,
                "_input_charset"    => trim(strtolower(C('input_charset')))
            );

            $alipay_config=C('ALIMOBILEPAY_CONFIG');

            //建立请求
            $alipaySubmit = new \AlipaySubmit($alipay_config);
            //建立请求，以表单HTML形式构造（默认），经测试post方法不行
            $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

            echo $html_text;
        }else{
            echo 'fail';
        }
    }
}
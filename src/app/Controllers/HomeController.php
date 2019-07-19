<?php
namespace App\Controllers;

use App\Models\DB;
use App\Views\JsonView;
use App\Views\View;

class HomeController{
    
    public function __construct(){
        
    }

    //Get all goods
    public function apiGoods(){
        $data = DB::getAll('goods'); //need to rename to goods table
        JsonView::render($data);
    }
    
    //Generate new goods
    public function apiGenerate(){
        $data = DB::generateData('goods');
        JsonView::render($data);
    }

    //Add new order
    public function addOrder(){
        $message = array();
        
        $total = DB::getTotal('goods');
        $add = DB::insertData('orders', $total);
        $message['order_id'] = $add;
        JsonView::render($message);
    }
    
    //Pay order
    public function payOrder($orderId){
        $message = array();
        
        $putdata = file_get_contents("php://input");
        $array = json_decode($putdata, true);
        $total = $array['total']; //total sum of order
        
        //get total sum of order and status (shoud be new)
        $order = DB::getOrder('orders', $orderId, $total);
        if($order){
            $ch = curl_init('https://ya.ru/');
            curl_exec($ch);
            if(!curl_errno($ch)){
                $info = curl_getinfo($ch);
                $response_code = $info['http_code'];
            }
            
            if($response_code == 200){
                //update order to paid
                $result = DB::paidOrder('orders', $orderId);
                $message['status'] = $result;
            }
            else{
                $message['status'] = "bad code";
            }
        }
        else{
            $message['status'] = "Order not found";
        }
        JsonView::render($message);
    }

    //Page not found
    public function errorNotFound(){
        $message = array('404', 'Page not found');
        View::render('home/message', $message);
    }
}
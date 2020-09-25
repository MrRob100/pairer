<?php

// declare(strict_types=1);

namespace App\Services;

use KuCoin\SDK\Auth;
use KuCoin\SDK\PrivateApi\Order;
use KuCoin\SDK\PrivateApi\Account;
use KuCoin\SDK\PublicApi\Symbol;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\OrderModel;

use KuCoin\SDK\Exceptions\HttpException;
use KuCoin\SDK\Exceptions\BusinessException;


/**
 * Class OrderService
 * @package App\Services
 */
class OrderService
{

    private $key = '5f497f6545dec70007316733';

    private $secret = '162fffc4-b77b-4cb8-aabd-7b80d40409c8';

    private $passphrase = 'Presspit';

    /**
     * To be run each day before the correction time
     */
    public function toUSDT()
    {
        //market order but log price at the time

        $auth = $this->getAuth();
        $order = new Order($auth);

        $amount = floor($this->getAvailableBalance('AMPL'));

        try {
            $params = [
                'clientOid' => uniqid(),
                'size' => $amount,
                'symbol'    => 'AMPL-USDT',
                'type'      => 'market',
                'side'      => 'sell',
                'remark'    => 'Back to USDT',
            ];

            $result = $order->create($params);
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }

        /* get order id for db */
        $orderId = $result['orderId'];

        /* find rough price for db */
        $symbol = new Symbol();
        $price = $symbol->getTicker('AMPL-USDT')['price'];
    }

    public function placeOrder($price)
    {

        // $amount = '20';
        $amount = $this->getAvailableBalance('USDT');

        $auth = $this->getAuth();
        $order = new Order($auth);

        try {
            $params = [
                'clientOid' => uniqid(),
                'price' => $price,
                'size' => $amount,
                'symbol'    => 'AMPL-USDT',
                'type'      => 'limit',
                'side'      => 'buy',
                'remark'    => 'AMPL order set',
            ];

            $result = $order->create($params);
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }

        /* Save record of order in db */
        $orderId = $result['orderId'];

        try {
            $order_model = new OrderModel();
            $order_model->order_id = $orderId;
            $order_model->coin_ordered = 'AMPL';
            $order_model->type = 'limit';
            $order_model->side = 'buy';
            $order_model->price = $price;
            $order_model->save();
            return [];
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }
    }

    public function getAuth()
    {
        $auth = new Auth($this->key, $this->secret, $this->passphrase);
        return $auth;
    }

    public function test()
    {

        $auth = $this->getAuth();
        $api = new Account($auth);

        try {
            $result = $api->getList(['type' => 'main']);
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
        }
    }

    public function getAvailableBalance($symbol)
    {
        $positions = $this->getPositions();
        foreach ($positions as $position) {
            if ($position['currency'] === $symbol) {
                $amount = $position['available'];
                return $amount;
            }
        }

        return '0';
    }

    public function getPrice($ticker)
    {
        $symbol = new Symbol();
        try {
            $price = $symbol->getTicker($ticker)['price'];
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return '0';
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return '0';
        }

        return $price;
    }

    public function getOrders()
    {

        $auth = $this->getAuth();
        $order = new Order($auth);
        $orders = $order->getList();
        return $orders;
    }

    public function getPositions()
    {

        $auth = $this->getAuth();
        $account = new Account($auth);

        try {
            //size order of positions
            //$positions = $account->getList();

            //of margin account
            // $positions = $account->getList(['type' => 'margin']);

            //of main account
            // $positions = $account->getList(['type' => 'main']);

            //of trading account
            $positions = $account->getList(['type' => 'trade']);
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }
        return $positions;
    }

    /**
     * checks db for orders then
     * outputs collection 
     */
    public function action($to_action)
    {
        //SORT OUT LOGIC IF THERE ARE MORE THAN 1 IN DB!!!!

        //PLACE ORDER

        //CREATE RECORD OF ORDER IN DB

        //THEN DELETE TRIGGER RECORD IN DB



        // $amount = '20'; OF USDT
        if ($to_action->first()->side === 'buy') {
            $amount = floor($this->getAvailableBalance('USDT') * ($to_action->first()->amount / 100));
        } elseif ($to_action->first()->side === 'sell') {
            $amount = floor($this->getAvailableBalance('AMPL') * ($to_action->first()->amount / 100));
        }

        $auth = $this->getAuth();
        $order = new Order($auth);

        try {

            $params = [
                'clientOid' => uniqid(),
                'size' => $amount,
                'symbol'    => $to_action->first()->pair,
                'type'      => $to_action->first()->type,
                'side'      => $to_action->first()->side,
                'remark'    => $to_action->first()->remark,
            ];

            if ($to_action->first()->price) {
                $params['price'] = $to_action->first()->price;
            }

            $result = $order->create($params);
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }



        //MAYBE FETCH PRICE IF MARKET
        /* Save record of order in db */
        $orderId = $result['orderId'];

        try {
            $order_model = new OrderModel();
            $order_model->order_id = $orderId;
            $order_model->coin_ordered = $to_action->first()->pair;
            $order_model->type = $to_action->first()->type;
            $order_model->side = $to_action->first()->side;
            $order_model->price = $price ? $price : '-';
            $order_model->save();
            return [];
        } catch (HttpException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        } catch (BusinessException $e) {
            dump($e->getMessage());
            Log::info($e->getMessage());
            return [];
        }
    }
}

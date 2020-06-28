<?php

namespace App\Http\Controllers;

use App\TradingHIstory;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

date_default_timezone_set('Asia/Tokyo');

class TradingHistoryController extends Controller
{
    protected $ORDER_TYPES = ['BUY' => '買', 'SELL' => '売'];
    protected $SYMBOLS = ['USDJPY' => 'USDJPY', 'N225_CFD', '日経225(CFD)', 'N225m' =>'日経225ミニ', 'BTC' => 'ビットコイン', 'DJI_CFD' => 'ダウ(CFD)', 'EURUSD' => 'EURUSD', 'OIL' => '原油', 'GBPJFY' => 'GBPJFY', 'AUDUSD', 'AUDUSD', 'GBPUSD' => 'GBPUSD'];

    public function index() {
        $trading_histories = $this->getTradingHIstories();
        $performance = $this->calcPerformance($trading_histories);

        $order_types = ['ALL' => '全て'] + $this->ORDER_TYPES;
        return view('trading-histories.index', compact('trading_histories'), ['pf' => $performance, 'symbols' =>$this->SYMBOLS, 'orderTypes' =>$order_types]);
    }

    public function create() {
        $th = new TradingHistory();
        $th->exit = 0;
        $th->entry = 0;
        $th->amount = 1;
        $th->profit = 0;
        $th->exit_dateTime = now();
        $c = new Carbon();
        $th->entry_dateTime = $c->subSecond(60);
        $th->symbol = 'USDJPY';
        $th->order_type = 'BUY';

        return $this->createViewWithReturn($th, 'create');
    }

    public function edit(TradingHistory $trading_history) {
        return $this->createViewWithReturn($trading_history, 'edit');
    }

    public function store() {
        $this->save();
        return redirect()->to('trading-history');
    }

    public function update(TradingHistory $trading_history) {
        $this->save($trading_history);
        return redirect()->to('trading-history');
    }

    public function destroy(TradingHistory $trading_history) {
        $trading_history->delete();
        return redirect()->to('trading-history');
    }

    function createViewWithReturn($th, $type) {
        $th->exit_dateTime = Carbon::parse($th->exit_dateTime)->format('Y-m-d\TH:i');
        $th->entry_dateTime = Carbon::parse($th->entry_dateTime)->format('Y-m-d\TH:i');
        return view('trading-histories.save', compact('th'), ['symbols'=>$this->SYMBOLS, 'orderTypes'=>$this->ORDER_TYPES, 'type'=>$type]);
    }

    function save($data = null) {
        request()->validate(['entty_dateTime' => 'before:exit_dateTime']);

        if ($data === null) {
            $data = new TradingHistory();
        }

        $data->memo = request('memo');
        $data->exit = request('exit');
        $data->entry = request('entry');
        $data->symbol = request('symbol');
        $data->amount = request('amount');
        $data->profit = request('profit');
        $data->order_type = request('order_type');
        $data->exit_dateTime = request('exit_dateTime');
        $data->entry_dateTime = request('entry_dateTime');
        $data->save();
    }

    function getTradingHistories() {
        $query = DB::table('trading_histories');

        if (request('symbol')) {
            $query->whereIn('symbol', request('symbol'));
        }

        if(array_key_exists(request('order_type'), $this->ORDER_TYPES)) {
            $query->where('order_type', request('order_type'));
        }

        if (request('date_from') && !request('date_to')) {
            $query->where('exit_dateTime', '>=', request('date_from'));
        } else if (!request('date_from') && request('date_to')) {
            $query->where('exit_dateTime', '<=', request('date_to'));
        } else if (request('date_from') && request('date_to')) {
            $date_to = request('date_to');
            $query->whereBetween('exit_dateTime', [request('date_from'), $date_to]);
        }

        return $query->orderBy('exit_dateTime', 'desc')->get();
    }

    function calcPerformance($trading_histories) {
        $ret = [
            'total_count' => 0,
            'total_profit' => 0,
            'win_count' => 0,
            'loss_count' => 0,
            'draw_count' => 0,
            'win_profit' => 0,
            'loss_profit' => 0,
            'win_rate' => 0,
            'profit_factor' => 0,
            'average_win_profit' => 0,
            'average_loss_profit' => 0
        ];

        foreach ($trading_histories as $th) {
            if($th->profit < 0) {
                $ret['loss_count']++;
                $ret['loss_profit'] += $th->profit;
            } else if ($th->profit > 0) {
                $ret['win_count']++;
                $ret['win_profit'] += $th->profit;
            } else {
                $ret['draw_count']++;
            }
            $ret['total_count']++;
            $ret['total_profit'] += $th->profit;
        }

        $win_loss_count = $ret['win_count'] + $ret['loss_count'];

        if (0 < $win_loss_count) {
            $ret['win_rate'] = round($ret['win_count'] / $win_loss_count *100);
        }

        if(0 > $ret['loss_profit']) {
            $ret['profit_factor'] = round($ret['win_profit'] / abs($ret['loss_profit']), 2);
        }

        if (0 < $ret['win_count']) {
            $ret['average_win_profit'] = round($ret['win_profit'] / $ret['win_count']);
        }

        if (0 < $ret['loss_count']) {
            $ret['average_loss_profit'] = round($ret['loss_profit'] / $ret['loss_count']);
        }
        return $ret;
    }
}

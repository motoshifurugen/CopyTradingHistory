<style>
* {
    font-size: 0.9rem;
}
h4 {
    padding: 0.25em 0.5em;
    border-left: solid 5px blue;
}
th {
    background-color:gray;
    color: white;
    font-weight: normal;
}
</style>

<form action="/trading-history">
    <table border="1" cellspacing="0" cellpadding="5">
        <tr align="left">
            <th>期間</th>
            <td>
                <input type="date" name="date_from" value=@if (request('date_from')) {{ request('date_from') }} @endif/>
                &nbsp~$nbsp
                <input type="date" name="date_to" value=@if(request('date_to')) {{ request('date_to') }} @endif/>
            </td>
        </tr>
        <tr align="left">
            <th>銘柄</th>
            <td>
                @foreach ($symbols as $key => $val)
                    <input type="checkbox" name="symbol[]" value="{{ $key }}" / @if (request('symbol') && in_array($key, request('symbol'))) checked @endif>{{ $val }}
                @endforeach
            </td>
        </tr>
        <tr align="left">
            <th>注文タイプ</th>
            <td>
                @foreach ($orderTypes as $key => $val)
                    <input type="radio" name="order_type" value="{{ $key }}" @if (!request('order_type') && $key == 'ALL' || request('order_type') == $key) checked @endif /> {{ $val }}
                @endforeach
            </td>
        </tr>
        <tr align="center">
            <td colspan="2">
                <button type="submit">検索</button>
                <button type="button" onclick="location.href='/trading-history'">リセット</button>
            </td>
        </tr>
    </table>
</form>

<h4>パフォーマンス</h4>
<table border="1" cellspacing="0" cellpadding="5">
    <tr align="center">
        <th>総回数</th>
        <th>総損益</th>
        <th>総利益</th>
        <th>総損失</th>
        <th>勝ち回数</th>
        <th>負け回数</th>
        <th>引き分け回数</th>
        <th>勝率</th>
        <th>PF</th>
        <th>平均利益</th>
        <th>平均損失</th>
    </tr>
    <tr align="right">
        <td>{{ $pf['total_count'] }}回</td>
        <td>{{ $pf['total_profit'] }}円</td>
        <td>{{ $pf['win_profit'] }}円</td>
        <td>{{ $pf['loss_profit'] }}円</td>
        <td>{{ $pf['win_count'] }}回</td>
        <td>{{ $pf['loss_count'] }}回</td>
        <td>{{ $pf['draw_count'] }}回</td>
        <td>{{ $pf['win_rate'] }}%</td>
        <td>{{ $pf['profit_factor'] }}</td>
        <td>{{ $pf['average_win_profit'] }}円</td>
        <td>{{ $pf['average_loss_profit'] }}円</td>
    </tr>
</table>

<h4>売買明細</h4>
<button type="button" onclick="location.href='/trading-history/create'">登録</button>
<br />
<br />
<table border="1" cellspacing="0" cellpadding="5">
    <tr align="center">
        <th>エントリー日時</th>
        <th>決済日時</th>
        <th>保有時間（分）</th>
        <th>銘柄</th>
        <th>注文タイプ</th>
        <th>注文数</th>
        <th>エントリー価格</th>
        <th>決済価格</th>
        <th>損益（円）</th>
        <th>メモ</th>
        <th colspan="2">操作</th>
    </tr>
    @foreach($trading_histories as $th)
    <tr>
        <td>{{ \Carbon\Carbon::create($th->entry_dateTime)->format('Y/m/d h:i') }}</td>
        <td>{{ \Carbon\Carbon::create($th->exit_dateTime)->format('Y/m/d h:i') }}</td>
        <td align="right">{{ \Carbon\Carbon::create($th->entry_dateTime)->diffInMinutes($th->exit_dateTime) }}</td>
        <td align="center">{{ $symbols[$th->symbol] }}</td>
        <td align="center">{{ $orderTypes[$th->order_type] }}</td>
        <td align="right">{{ $th->amount }}</td>
        <td align="right">{{ $th->entry }}</td>
        <td align="right">{{ $th->exit }}</td>
        <td align="right">{{ $th->profit }}</td>
        <td>{{ $th->memo }}</td>
        <td><button type="button" onclick="location.href='/trading-history/{{ $th->id }}/edit'">編集</button></td>
        <td valign="middle">
            <form action="/trading-history/{{ $th->id }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type ="submit">削除</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>


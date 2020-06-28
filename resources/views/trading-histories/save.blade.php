<style>
* {
    font-size: 0.9rem;
}
h4 {
    padding: 0.25em 0.5em;
    border-left: solid 5px blue;
}
th {
    background-color: gray;
    color: white;
    font-weight: normal;
}
</style>

@if ($type == 'create')
<form method="POST" action="/trading-history">
@else
<form method="POST" action="/trading-history/{{ $th->id }}">
    @method('PUT')
@endif

@csrf
<table border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>エントリー日時</th>
        <td><input type="datetime-local" name="entry_dateTime" value="{{ $th->entry_dateTime }}" required></td>
    </tr>
    <tr>
        <th>決済日時</th>
        <td><input type="datetime-local" name="exit_dateTime" value="{{ $th->exit_dateTime }}" required></td>
    </tr>
    <tr>
        <th>銘柄</th>
        <td>
            <select name="symbol">
                @foreach ($symbols as $key => $val)
                    <option value="{{ $key }}" @if ($th->symbol == $key) selected @endif>{{ $val }}</option>
                @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <th>注文タイプ</th>
        <td>
            @foreach ($orderTypes as $key => $val)
                <input name="order_type" type="radio" value="{{ $key }}" @if ($th->order_type == $key) checked @endif>{{ $val }}
            @endforeach
        </td>
    </tr>
    <tr>
        <th>注文数</th>
        <td><input type="number" name="amount" value="{{ $th->amount }}" required></td>
    </tr>
    <tr>
        <th>エントリー価格</th>
        <td><input type="number" name="entry" value="{{ $th->entry }}" required></td>
    </tr>
    <tr>
        <th>決済価格</th>
        <td><input type="number" name="exit" value="{{ $th->exit }}" required></td>
    </tr>
    <tr>
        <th>損益(円)</th>
        <td><input type="number" name="profit" value="{{ $th->profit }}" required></td>
    </tr>
    <tr>
        <th>メモ</th>
        <td><textarea name="memo" value="{{ $th->memo }}"></textarea></td>
    </tr>
    <tr align="center">
        <td colspan="2"><button type="submit">{{ $type == 'create' ? '登録' : '更新' }}</button></td>
    </tr>
</table>
</form>

@if ($errors->any())
<ul>
    @foreach($errors->all() as $error)
        <li style="color:red">{{ $error }}</li>
    @endforeach
</ul>
@endif

<a href="/trading-history">戻る</a>


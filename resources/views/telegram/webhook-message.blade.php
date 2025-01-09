👥 <a href="{{$accountUrl}}"><i>{{$accountName}}</i></a>
-----------
@if(isset($itemUrl, $itemTitle) && $itemUrl && $itemTitle)
📣 <a href="{{$itemUrl}}"><i>{{$itemTitle}}</i></a>
-----------
@endif
@if(isset($price))
💰 <i>{{$price}}</i>
-----------
@endif
🙋🏻‍ {{$clientName}} отправил:

{!! $message !!}

<span class="tg-spoiler">__ids__: [{{$accountId}},{{$chatId}}]</span>

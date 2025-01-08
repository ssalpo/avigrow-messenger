👥 <a href="{{$accountUrl}}"><i>{{$accountName}}</i></a>
-----------
@if(!$itemUrl || !$itemTitle)
📣 <a href="{{$itemUrl}}"><i>{{$itemTitle}}</i></a>
-----------
@endif
@if($price)
💰 <i>{{$price}}</i>
-----------
@endif
🙋🏻‍ {{$clientName}} отправил:

{!! $message !!}

<span class="tg-spoiler">__ids__: [{{$accountId}},{{$chatId}}]</span>

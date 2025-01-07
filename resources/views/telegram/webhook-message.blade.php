👥 <a href="{{$accountUrl}}"><i>{{$accountName}}</i></a>
-----------
@isset($itemUrl, $itemTitle)
📣 <a href="{{$itemUrl}}"><i>{{$itemTitle}}</i></a>
@endisset
-----------
💰 <i>{{$price}}</i>
-----------
🙋🏻‍ {{$clientName}} отправил:

{!! $message !!}

<span class="tg-spoiler">__ids__: [{{$accountId}},{{$chatId}}]</span>

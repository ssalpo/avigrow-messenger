👥 <a href="{{$accountUrl}}"><i>{{$accountName}}</i></a>
-----------
@isset($itemUrl, $itemTitle)
📣 <a href="{{$itemUrl}}"><i>{{$itemTitle}}</i></a>
-----------
@endisset
@isset($price)
💰 <i>{{$price}}</i>
-----------
@endisset
🙋🏻‍ {{$clientName}} отправил:

{!! $message !!}

<span class="tg-spoiler">__ids__: [{{$accountId}},{{$chatId}}]</span>

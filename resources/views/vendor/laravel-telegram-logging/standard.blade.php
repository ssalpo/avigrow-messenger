<b>{{ $appName }}</b> ({{ $level_name }}) @if($level_name !== 'INFO') @ssalpo @endif
Env: {{ $appEnv }}
[{{ $datetime->format('Y-m-d H:i:s') }}] {{ $appEnv }}.{{ $level_name }} {{ $formatted }}

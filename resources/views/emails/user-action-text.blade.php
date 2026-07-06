{!! $brandName !!}
{!! $heading !!}

{!! $messageLine !!}
@if(!empty($details))

@foreach($details as $label => $value)
{!! $label !!}: {!! $value !!}
@endforeach
@endif
@if($actionLabel && $actionUrl)

{!! $actionLabel !!}: {!! $actionUrl !!}
@endif

This is an automated account update email.

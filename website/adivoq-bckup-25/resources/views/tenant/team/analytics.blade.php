@extends('layouts.tenant')

@section('title','Team Analytics')
@section('page_title','Team Analytics')

@section('content')
<div class="glass rounded-2xl p-6 space-y-8">

    <h3 class="text-lg font-semibold text-white mb-6">Team Analytics</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <div>
            <h4 class="text-white font-semibold mb-2">Revenue by Member</h4>
            <ul>
                @foreach($revenue as $name => $amount)
                    <li class="flex justify-between">
                        <span>{{ $name }}</span>
                        <span class="text-green-400 font-semibold">₹{{ number_format($amount,0) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-2">Activity by Member</h4>
            <ul>
                @foreach($activity as $name => $count)
                    <li class="flex justify-between">
                        <span>{{ $name }}</span>
                        <span class="text-blue-400 font-semibold">{{ $count }} actions</span>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>

</div>
@endsection
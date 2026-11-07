@extends('frontend.layout')
@section('title', $game->name.' Result Chart '.$year.' | '.$siteName)
@section('description', 'Date-wise saved result records for '.$game->name.' in '.\Carbon\Carbon::create($year,$month,1)->format('F Y').'.')
@section('canonical', $canonical)
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">{{ $game->name }} — Monthly Result Chart</div><div class="freshness-updated">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</div></section>
<section class="seo-box quick-record-box">
    <div class="result-heading"><h1>{{ $game->name }} Record Chart</h1><p><a href="{{ route('home') }}">Home</a> / <a href="{{ route('charts.index') }}">Chart Archive</a> / {{ $game->name }}</p></div>
    <form method="get" class="chart-filter-form">
        <label>Month <select name="month" class="chart-select">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m===$month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>@endfor</select></label>
        <label>Year <select name="year" class="chart-select">@for($y=now(config('gali.timezone'))->year;$y>=2014;$y--)<option value="{{ $y }}" @selected($y===$year)>{{ $y }}</option>@endfor</select></label>
        <button type="submit" class="chart-submit">View Chart</button>
    </form>
    <div class="table-scroll"><table class="khaiwal-table"><thead><tr><th>Date</th><th>Result</th><th>Status</th></tr></thead><tbody>@foreach($rows as $row)<tr><th>{{ $row['date']->format('d M Y') }}</th><td>{{ $row['number'] }}</td><td>{{ $row['number'] === '--' ? 'Pending / Not published' : 'Published' }}</td></tr>@endforeach</tbody></table></div>
</section>
<section class="seo-box"><div class="result-heading"><h2>{{ $game->name }} Historical Archive</h2><p>Choose any year and month with saved records.</p></div>
    @forelse($availableArchives as $archiveYear=>$months)
        <div class="chart-year-group"><h3>{{ $archiveYear }}</h3><div class="homepage-archive-months">@foreach($months as $archiveMonth)<a href="{{ route('market',['slug'=>$game->slug,'year'=>$archiveYear,'month'=>$archiveMonth]) }}">{{ \Carbon\Carbon::create()->month($archiveMonth)->format('F') }}</a>@endforeach</div></div>
    @empty<p>No older published records are available.</p>@endforelse
</section>
@endsection

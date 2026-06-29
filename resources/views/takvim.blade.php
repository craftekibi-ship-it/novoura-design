@extends('layouts.board')
@section('title','Takvim')
@section('n_takvim','on')

@php
  use Carbon\Carbon;
  $first = Carbon::create($y, $m, 1);
  $daysInMonth = $first->daysInMonth;
  $offset = $first->dayOfWeekIso - 1; // Pazartesi=0
  $prev = $first->copy()->subMonth()->format('Y-m');
  $next = $first->copy()->addMonth()->format('Y-m');
  $aylar = [1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık'];
  $bugun = now()->format('Y-m-d');
@endphp

@section('content')
<style>
  .cal-top{display:flex;align-items:center;gap:16px;margin-bottom:18px}
  .cal-top h1{margin:0}
  .layout{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
  .cal{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
  .dow{font-size:12px;color:var(--muted);text-align:center;padding:4px 0;font-weight:600}
  .cell{background:var(--panel);border:1px solid var(--line);border-radius:10px;min-height:96px;padding:6px;display:flex;flex-direction:column;gap:4px}
  .cell.blank{background:transparent;border:none}
  .cell .dnum{font-size:12px;color:var(--muted)}
  .cell.today{border-color:var(--amber)}
  .cell.today .dnum{color:var(--amber);font-weight:700}
  .chips{display:flex;flex-wrap:wrap;gap:3px}
  .chip{width:30px;height:38px;border-radius:5px;object-fit:cover;border:1px solid var(--line)}
  .chip-txt{font-size:9px;line-height:1.15;padding:3px 5px;border-radius:5px;background:#5a3d18;color:var(--amber);border:1px solid var(--line)}
  .side{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:16px}
  .side h3{margin:0 0 4px;font-size:15px}
  .side .row{display:flex;gap:8px;align-items:center;padding:10px 0;border-bottom:1px solid var(--line)}
  .side .row:last-child{border-bottom:none}
  .side img{width:34px;height:42px;border-radius:5px;object-fit:cover;border:1px solid var(--line)}
  .side .nm{font-size:13px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .empty{color:var(--muted);font-size:13px;padding:10px 0}
</style>

<div class="cal-top">
  <a class="btn" href="?ay={{ $prev }}">←</a>
  <h1>{{ $aylar[$m] }} {{ $y }}</h1>
  <a class="btn" href="?ay={{ $next }}">→</a>
  <a class="btn btn-amber" href="/plan/pdf?ay={{ $ay }}" style="margin-left:auto">📄 Aylık plan PDF</a>
</div>

<div class="layout">
  <div>
    <div class="cal">
      @foreach(['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'] as $d)<div class="dow">{{ $d }}</div>@endforeach
      @for($i=0;$i<$offset;$i++)<div class="cell blank"></div>@endfor
      @for($d=1;$d<=$daysInMonth;$d++)
        @php $ds = sprintf('%04d-%02d-%02d',$y,$m,$d); $dayPosts = $planli[$ds] ?? collect(); @endphp
        <div class="cell {{ $ds===$bugun?'today':'' }}">
          <span class="dnum">{{ $d }}</span>
          <div class="chips">
            @foreach($dayPosts as $p)
              @if($p->export_yolu)
                <img class="chip" src="{{ asset('storage/'.$p->export_yolu) }}" title="{{ $p->catalogItem->ad ?? '' }}">
              @else
                <span class="chip-txt" title="Foto bekliyor · {{ $p->catalogItem->ad ?? '' }}">{{ \Illuminate\Support\Str::limit($p->catalogItem->ad ?? '—', 12) }}</span>
              @endif
            @endforeach
          </div>
        </div>
      @endfor
    </div>
  </div>

  <div class="side">
    <h3>Planlanmamış</h3>
    <p class="empty" style="padding-top:0">Bir tarihe ata → takvime düşsün.</p>
    @forelse($planlanmamis as $p)
      <div class="row">
        @if($p->export_yolu)<img src="{{ asset('storage/'.$p->export_yolu) }}">@endif
        <span class="nm" title="{{ $p->catalogItem->ad ?? '' }}">{{ $p->catalogItem->ad ?? '—' }}</span>
        <input type="date" id="pd-{{ $p->id }}" style="width:130px">
        <button class="btn" onclick="ata({{ $p->id }})">Ata</button>
      </div>
    @empty
      <div class="empty">Planlanacak post yok.</div>
    @endforelse
  </div>
</div>
@endsection

@section('scripts')
<script>
  async function ata(id){ const t=document.getElementById('pd-'+id).value; if(!t){ alert('Tarih seç'); return; } try{ await api('/posts/'+id+'/planla','POST',{tarih:t}); location.reload(); }catch(e){ alert(e.message); } }
</script>
@endsection

{{-- Jhon | 2026-11-25 19:52 WITA | TAMBAH: Renderer rekursif mempertahankan field/kelompok API, tanpa menambah angka atau total buatan. --}}
@php
    $label = static function ($key) { return ucwords(str_replace('_', ' ', preg_replace('/([a-z])([A-Z])/', '$1 $2', (string)$key))); };
    $isList = is_array($value) && ($value === [] || array_keys($value) === range(0,count($value)-1));
    $tableRows = $isList && count($value) && count(array_filter($value,'is_array')) === count($value);
    $columns = [];
    if ($tableRows) foreach ($value as $row) foreach (array_keys($row) as $key) if (!in_array($key,$columns,true)) $columns[] = $key;
@endphp
@if($depth > 10)
<pre>{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
@elseif($value === [] || $value === null)
<p class="muted">Tidak ada data pada bagian ini untuk periode yang dipilih.</p>
@elseif($tableRows)
<div class="table-wrap"><table><thead><tr>@foreach($columns as $key)<th>{{ $label($key) }}</th>@endforeach</tr></thead><tbody>
@foreach($value as $row)<tr>@foreach($columns as $key)<td>
@if(is_array($row[$key] ?? null))<details><summary>Lihat rincian</summary>@include('sirs.report-data',['value'=>$row[$key], 'depth'=>$depth+1])</details>
@elseif(is_bool($row[$key] ?? null)){{ $row[$key] ? 'Ya' : 'Tidak' }}
@else{{ $row[$key] ?? '—' }}@endif
</td>@endforeach</tr>@endforeach</tbody></table></div>
@elseif(is_array($value))
@php $scalars = array_filter($value, static function($item) { return !is_array($item); }); @endphp
@if(count($scalars))<table class="kv"><tbody>@foreach($scalars as $key=>$item)@if($key !== 'message')<tr><th>{{ $label($key) }}</th><td>{{ is_bool($item) ? ($item ? 'Ya':'Tidak') : ($item ?? '—') }}</td></tr>@endif @endforeach</tbody></table>@endif
@foreach($value as $key=>$item)@if(is_array($item))<section class="data-section">
@if($key === 'data_pasien')<details><summary>Rincian data pasien</summary>@include('sirs.report-data',['value'=>$item,'depth'=>$depth+1])</details>
@else @if($key !== 'data')<h3>{{ $label($key) }}</h3>@endif @include('sirs.report-data',['value'=>$item,'depth'=>$depth+1]) @endif
</section>@endif @endforeach
@else<span>{{ is_bool($value) ? ($value ? 'Ya':'Tidak') : $value }}</span>@endif

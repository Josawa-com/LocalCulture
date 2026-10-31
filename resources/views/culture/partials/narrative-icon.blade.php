{{--
    Ikon narasi budaya (dipakai di tampilan desktop dan kartu mobile).
    Parameter: $icon = islands | heritage | exchange | preserve | (lainnya = goal)
--}}
@props(['icon' => 'goal'])

@if($icon === 'islands')
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="38" cy="42" r="18" stroke="#A86E1E" stroke-width="5"/>
        <circle cx="66" cy="62" r="12" stroke="#A86E1E" stroke-width="5"/>
        <path d="M12 78c12-8 24-8 36 0s24 8 36 0" stroke="#A86E1E" stroke-width="5" stroke-linecap="round"/>
    </svg>
@elseif($icon === 'heritage')
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M20 44 50 18l30 26" stroke="#A86E1E" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M28 44v34M72 44v34M20 78h60" stroke="#A86E1E" stroke-width="5" stroke-linecap="round"/>
        <path d="M42 78V58h16v20" stroke="#A86E1E" stroke-width="5" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'exchange')
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M22 38h44l-10-10M78 62H34l10 10" stroke="#A86E1E" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="50" cy="50" r="34" stroke="#A86E1E" stroke-width="4" stroke-dasharray="6 8"/>
    </svg>
@elseif($icon === 'preserve')
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M50 12 80 24v22c0 18-12 32-30 40-18-8-30-22-30-40V24Z" stroke="#A86E1E" stroke-width="5" stroke-linejoin="round"/>
        <path d="m38 50 8 8 16-16" stroke="#A86E1E" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@else
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="50" cy="42" r="20" stroke="#A86E1E" stroke-width="5"/>
        <path d="m62 58 14 22-12-4-4 12-14-22" stroke="#A86E1E" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="50" cy="42" r="6" fill="#A86E1E"/>
    </svg>
@endif

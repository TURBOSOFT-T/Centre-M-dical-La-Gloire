@props(['statut'])

@php
    $config = match ($statut) {
        'planifie' => [
            'label' => 'Planifié',
            'class' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'dot'   => 'bg-amber-500',
        ],
        'confirme' => [
            'label' => 'Confirmé',
            'class' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            'dot'   => 'bg-blue-500',
        ],
        'en_attente' => [
            'label' => 'En attente',
            'class' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
            'dot'   => 'bg-purple-500',
        ],
        'honore' => [
            'label' => 'Honoré',
            'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'dot'   => 'bg-emerald-500',
        ],
        'annule' => [
            'label' => 'Annulé',
            'class' => 'bg-rose-50 text-rose-700 ring-rose-600/10',
            'dot'   => 'bg-rose-500',
        ],
        'absent' => [
            'label' => 'Absent',
            'class' => 'bg-slate-100 text-slate-700 ring-slate-600/10',
            'dot'   => 'bg-slate-500',
        ],
        default => [
            'label' => ucfirst($statut ?? 'Inconnu'),
            'class' => 'bg-slate-50 text-slate-600 ring-slate-500/10',
            'dot'   => 'bg-slate-400',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $config['class']]) }}>
    <svg class="h-1.5 w-1.5 {{ $config['dot'] }} rounded-full" viewBox="0 0 6 6" aria-hidden="true">
        <circle cx="3" cy="3" r="3" />
    </svg>
    {{ $config['label'] }}
</span>
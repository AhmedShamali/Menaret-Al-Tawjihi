@php
    $convId = is_object($conv) ? $conv->id : ($conv['id'] ?? '');
    $convTitle = is_object($conv) ? $conv->title : ($conv['title'] ?? 'محادثة سابقة');
    $convMode = is_object($conv) ? ($conv->symbol_mode ?? 'ar') : ($conv['symbol_mode'] ?? 'ar');
    $convDate = is_object($conv) ? ($conv->created_at ? $conv->created_at->diffForHumans() : '') : '';
@endphp
<div class="conversation-nav-item" data-id="{{ $convId }}" data-title="{{ $convTitle }}">
    <div class="conversation-main-click" onclick="loadConversation('{{ $convId }}')">
        <i class="fa-regular fa-message conv-msg-icon"></i>
        <div class="conv-info">
            <span class="conv-title-text" title="{{ $convTitle }}">{{ $convTitle }}</span>
            <span class="conv-meta-date">
                {{ $convDate }} &bull;
                <span class="mode-tag">{{ $convMode === 'en' ? 'EN' : 'عربي' }}</span>
            </span>
        </div>
    </div>
    <div class="conv-actions-group">
        <button type="button" class="btn-conv-action btn-rename-conv" title="{{ __('إعادة تسمية') }}" onclick="openRenameModalForId('{{ $convId }}', event)">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button type="button" class="btn-conv-action btn-delete-conv" title="{{ __('حذف') }}" onclick="openDeleteModalForId('{{ $convId }}', event)">
            <i class="fa-regular fa-trash-can"></i>
        </button>
    </div>
</div>

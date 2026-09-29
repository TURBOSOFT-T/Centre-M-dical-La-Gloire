<div class="d-contents" wire:poll.10s>
    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" 
       href="javascript:;" 
       data-bs-toggle="dropdown" 
       aria-expanded="false">
        <i class="ri-mail-open-line vertical-align-middle" style="font-size: 22px;"></i>
        @if($unreadCount > 0)
            <span class="msg-count bg-success" style="background-color: #28a745 !important;">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
    
    <div class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 320px; max-height: 450px; overflow-y: auto;">
        <div class="msg-header d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
            <h6 class="msg-header-title mb-0" style="font-weight: 600;">Messages Clients</h6>
            <span class="badge bg-soft-success text-success">{{ $unreadCount }} Nouveaux</span>
        </div>
        
        <div class="msg-header-content p-0">
            @forelse($recentMessages as $message)
                <a class="dropdown-item d-flex align-items-center py-3 border-bottom " 
                   href="{{ route('admin_contact_form') }}?id={{ $message->id }}"
                   style="white-space: normal;">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-md bg-soft-primary rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; background-color: #e0f2fe; color: #0369a1;">
                            <i class="ri-user-voice-line" style="font-size: 18px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="msg-name mb-1" style="font-size: 13px; font-weight: 600;">
                            {{ $message->nom ?? $message->email }}
                        </h6>
                        <p class="msg-info mb-1 text-muted" style="font-size: 12px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $message->message }}
                        </p>
                        <p class="msg-time mb-0 text-muted" style="font-size: 11px;">
                            <i class="ri-time-line me-1"></i>{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="ri-mail-send-line d-block mb-2" style="font-size: 32px; color: #cbd5e1;"></i>
                    <span style="font-size: 13px;">Aucun message reçu</span>
                </div>
            @endforelse
        </div>
        
        <div class="text-center p-2 border-top bg-light">
            <a href="{{ route('admin_contact_form') }}" class="btn btn-sm btn-link text-primary p-0 font-weight-bold" style="font-size: 13px; text-decoration: none;">
                Voir toute la messagerie <i class="ri-arrow-right-line align-middle ms-1"></i>
            </a>
        </div>
    </div>
</div>
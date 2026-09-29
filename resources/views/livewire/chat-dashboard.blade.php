<div class="chat-admin-container" wire:poll.4s>
    <!-- Colonne Gauche : Tous les clients entrants -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-comments"></i> Fil d'attente</h3>
        </div>
        <div class="sidebar-list">
            @if($discussions->isEmpty())
                <div class="no-chats">Aucun client en ligne.</div>
            @else
                @foreach($discussions as $disc)
                    <div class="chat-item {{ $sessionActive === $disc['session_id'] ? 'active' : '' }}" 
                         wire:click="selectionnerSession('{{ $disc['session_id'] }}')">
                        <div class="chat-avatar"><i class="fas fa-user-circle"></i></div>
                        <div class="chat-info">
                            <div class="chat-info-row">
                                <span class="client-name">Client #{{ substr($disc['session_id'], 0, 6) }}</span>
                                <span class="chat-time">{{ $disc['temps'] }}</span>
                            </div>
                            <div class="chat-preview">{{ Str::limit($disc['aperçu'], 35) }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Colonne Droite : Gestion Multi-Bulles / Onglets -->
    <div class="chat-main-area">
        @if(count($sessionsOuvertes) > 0)
            <!-- Barre des Onglets Multi-Clients -->
            <div class="chat-tabs-bar">
                @foreach($sessionsOuvertes as $openSession)
                    <div class="chat-tab {{ $sessionActive === $openSession ? 'active' : '' }}">
                        <span wire:click="selectionnerSession('{{ $openSession }}')">
                            <i class="fas fa-comment-alt"></i> Client #{{ substr($openSession, 0, 6) }}
                        </span>
                        <button class="close-tab-btn" wire:click.stop="fermerOnglet('{{ $openSession }}')">&times;</button>
                    </div>
                @endforeach
            </div>

            <!-- Contenu de la Discussion Active -->
            @if($sessionActive)
                <div class="main-body" id="admin-messages-box">
                    @foreach($messages as $msg)
                        <div class="admin-chat-row {{ $msg->expediteur === 'admin' ? 'admin-right' : 'client-left' }}">
                            <div class="admin-chat-bubble">
                                <p class="msg-content">{{ $msg->contenu }}</p>
                                <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Formulaire de réponse -->
                <form wire:submit.prevent="envoyer" class="main-footer">
                    <input type="text" wire:model="nouveauMessage" placeholder="Répondre au Client #{{ substr($sessionActive, 0, 6) }}..." required>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            @endif
        @else
            <!-- Mode veille si aucune bulle ouverte -->
            <div class="chat-placeholder">
                <i class="far fa-comments"></i>
                <p>Cliquez sur un ou plusieurs clients à gauche pour ouvrir leurs bulles de discussion en simultané.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollAdminBottom = () => {
                const box = document.getElementById('admin-messages-box');
                if (box) { box.scrollTop = box.scrollHeight; }
            };
            scrollAdminBottom();
            Livewire.on('scroll-admin-chat', () => { setTimeout(scrollAdminBottom, 50); });
        });
    </script>

    <style>
    /* Style de la barre d'onglets supérieure */
    .chat-tabs-bar {
        display: flex;
        background-color: #f0f2f5;
        border-bottom: 1px solid #e0e0e0;
        padding: 5px 10px 0 10px;
        gap: 5px;
        overflow-x: auto;
    }

    /* Style d'une bulle / onglet client */
    .chat-tab {
        display: flex;
        align-items: center;
        background-color: #e4e6eb;
        padding: 8px 14px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #4b4b4b;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        border-bottom: none;
        white-space: nowrap;
    }

    .chat-tab span i {
        margin-right: 6px;
        color: #888;
    }

    .chat-tab:hover {
        background-color: #d8dadf;
    }

    /* Onglet actif mis en évidence */
    .chat-tab.active {
        background-color: #efeae2; /* Match avec la zone de chat arrière-plan */
        color: #ff9800;
        border-color: #e0e0e0;
    }

    .chat-tab.active span i {
        color: #ff9800;
    }

    /* Bouton de fermeture d'onglet (X) */
    .close-tab-btn {
        background: none;
        border: none;
        font-size: 16px;
        margin-left: 10px;
        color: #888;
        cursor: pointer;
        line-height: 1;
        padding: 0 4px;
        border-radius: 50%;
    }

    .close-tab-btn:hover {
        background-color: rgba(0,0,0,0.1);
        color: #333;
    }
</style>
</div>
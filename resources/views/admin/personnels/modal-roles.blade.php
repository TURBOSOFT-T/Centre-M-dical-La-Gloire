  <div class="modal fade" id="roleModal{{ $personnel->id }}" tabindex="-1"
      role="dialog" aria-hidden="true">
      <div class="modal-dialog">
          <form
              action="{{ route('admin.personnels.updateRole', $personnel->id) }}"
              method="POST">
              @csrf
             <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ \App\Helpers\TranslationHelper::TranslateText('Modifier le rôle') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">{{ \App\Helpers\TranslationHelper::TranslateText('Nouveau rôle') }} :</label>
                    <select class="form-control" name="role" required>
                        


                        <option value="admin" {{ $personnel->role == 'admin' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Admin') }}
                        </option>
                        <option value="caisse" {{ $personnel->role == 'caisse' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Caisse') }}
                        </option>
                        <option value="commercial" {{ $personnel->role == 'commercial' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Commercial') }}
                        </option>

                        <option value="comptable" {{ $personnel->role == 'comptable' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Comptable') }}
                        </option>
                        <option value="secretaire" {{ $personnel->role == 'secretaire' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Secrétaire') }}
                        </option>

                          <option value="medecin" {{ $personnel->role == 'medecin' ? 'selected' : '' }}>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Médecin') }}
                        </option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        {{ \App\Helpers\TranslationHelper::TranslateText('Annuler') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        {{ \App\Helpers\TranslationHelper::TranslateText('Enregistrer') }}
                    </button>
                </div>
            </div>
          </form>
      </div>
  </div>
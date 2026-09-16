  <div class="modal fade" id="roleModal{{ $shop->id }}" tabindex="-1"
      role="dialog" aria-hidden="true">
      <div class="modal-dialog">
          <form
              action="{{ route('admin.shops.updateRoleShop', $shop->id) }}"
              method="POST">
              @csrf
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Modifier le rôle</h5>
                      <button type="button" class="btn-close"
                          data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">
                      <label>Nouveau rôle :</label>
                      <select class="form-control" name="role" required>
                          <option value="user"
                              {{ $shop->role == 'user' ? 'selected' : '' }}>
                              Utilisateur</option>
                          <option value="shop"
                              {{ $shop->role == 'shop' ? 'selected' : '' }}>
                              shop</option>
                          <option value="admin"
                              {{ $shop->role == 'admin' ? 'selected' : '' }}>
                              Admin</option>
                          <option value="commercial"
                              {{ $shop->role == 'commercial' ? 'selected' : '' }}>
                              serveur</option>
                      </select>
                  </div>

                  <div class="modal-footer">
                      <button type="button" class="btn btn-light"
                          data-bs-dismiss="modal">Annuler</button>
                      <button type="submit"
                          class="btn btn-success">Enregistrer</button>
                  </div>
              </div>
          </form>
      </div>
  </div>
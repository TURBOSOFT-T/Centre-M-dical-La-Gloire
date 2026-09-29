    <!-- Center modal content -->
    <div class="modal fade" id="shop-{{ $shop->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myCenterModalLabel">
                        Configuration des accès du shop
                        <b class="text-capitalize"> {{ $shop->nom }} </b>.
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update-shop-permissions') }}" method="post">
                    <input type="hidden" name="id" value="{{$shop->id}}">
                    @csrf
                    <div class="modal-body text-start">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Dashborad</b>
                                    </td>
                                    <td colspan="4">
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dashboard" @checked($shop->hasPermissionTo('dashboard')) > Voir
                                    </td>
                                </tr>



                                <tr>
                                    <td>
                                        <b>Service</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="service_view" @checked($shop->hasPermissionTo('service_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="service_add" @checked($shop->hasPermissionTo('service_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="service_edit" @checked($shop->hasPermissionTo('service_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="service_delete" @checked($shop->hasPermissionTo('service_delete'))> Supprimer
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Table</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_view" @checked($shop->hasPermissionTo('table_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_add" @checked($shop->hasPermissionTo('table_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_edit" @checked($shop->hasPermissionTo('table_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_delete" @checked($shop->hasPermissionTo('table_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Clients</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="clients_view" @checked($shop->hasPermissionTo('clients_view')) > Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="clients_delete" @checked($shop->hasPermissionTo('clients_delete'))> Supprimer
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Produit</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_view" @checked($shop->hasPermissionTo('product_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_add" @checked($shop->hasPermissionTo('product_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_edit" @checked($shop->hasPermissionTo('product_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_delete" @checked($shop->hasPermissionTo('product_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Gestion de stock</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="gestion_stock" @checked($shop->hasPermissionTo('gestion_stock'))> Ajouter du stock
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Commande</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_view" @checked($shop->hasPermissionTo('order_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_add" @checked($shop->hasPermissionTo('order_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_edit" @checked($shop->hasPermissionTo('order_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_delete" @checked($shop->hasPermissionTo('order_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Paramètres</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="setting_view" @checked($shop->hasPermissionTo('setting_view'))> Voir
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary">
                            Mettre a jour les permissions
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
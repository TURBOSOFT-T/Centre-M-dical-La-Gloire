    <!-- Center modal content -->
    <div class="modal fade" id="personnel-{{ $personnel->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myCenterModalLabel">
                        Configuration des accès du personnel
                        <b class="text-capitalize"> {{ $personnel->nom }} </b>.
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update-personnel-permissions') }}" method="post">
                    <input type="hidden" name="id" value="{{$personnel->id}}">
                    @csrf
                   <div class="modal-body text-start  table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Dashborad</b>
                                    </td>
                                    <td colspan="4">
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dashboard" @checked($personnel->hasPermissionTo('dashboard')) > Voir
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Categories</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="category_view" @checked($personnel->hasPermissionTo('category_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="category_add" @checked($personnel->hasPermissionTo('category_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="category_edit" @checked($personnel->hasPermissionTo('category_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="category_delete" @checked($personnel->hasPermissionTo('category_delete'))> Supprimer
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Marques</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="marque_view" @checked($personnel->hasPermissionTo('marque_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="marque_add" @checked($personnel->hasPermissionTo('marque_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="marque_edit" @checked($personnel->hasPermissionTo('marque_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="marque_delete" @checked($personnel->hasPermissionTo('marque_delete'))> Supprimer
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Code promo</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="coupon_view" @checked($personnel->hasPermissionTo('coupon_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="coupon_add" @checked($personnel->hasPermissionTo('coupon_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="coupon_edit" @checked($personnel->hasPermissionTo('coupon_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="coupon_delete" @checked($personnel->hasPermissionTo('coupon_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Clients</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="clients_view" @checked($personnel->hasPermissionTo('clients_view')) > Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="clients_delete" @checked($personnel->hasPermissionTo('clients_delete'))> Supprimer
                                    </td>
                                    <td colspan="2"></td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Témoignages</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="testimonial_view" @checked($personnel->hasPermissionTo('testimonial_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="testimonial_add" @checked($personnel->hasPermissionTo('testimonial_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="testimonial_edit" @checked($personnel->hasPermissionTo('testimonial_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="testimonial_delete" @checked($personnel->hasPermissionTo('testimonial_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b> Service client</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="message_view" @checked($personnel->hasPermissionTo('message_view')) > Voir
                                    </td>

                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Produit</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_view" @checked($personnel->hasPermissionTo('product_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_add" @checked($personnel->hasPermissionTo('product_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_edit" @checked($personnel->hasPermissionTo('product_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="price_view" @checked($personnel->hasPermissionTo('price_view'))> Voir prix d'achat
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="product_delete" @checked($personnel->hasPermissionTo('product_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Gestion de stock</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="gestion_stock" @checked($personnel->hasPermissionTo('gestion_stock'))> Ajouter du stock
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Commande</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_view" @checked($personnel->hasPermissionTo('order_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_add" @checked($personnel->hasPermissionTo('order_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_edit" @checked($personnel->hasPermissionTo('order_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="live_order_edit" @checked($personnel->hasPermissionTo('live_order_edit'))>Modifier Status commande
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_delete" @checked($personnel->hasPermissionTo('order_delete'))> Supprimer
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <b>Boutique</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_view" @checked($personnel->hasPermissionTo('table_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_add" @checked($personnel->hasPermissionTo('table_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_edit" @checked($personnel->hasPermissionTo('table_edit'))> Modifier
                                    </td>


                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="table_delete" @checked($personnel->hasPermissionTo('table_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Paramètres</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="setting_view" @checked($personnel->hasPermissionTo('setting_view'))> Voir
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
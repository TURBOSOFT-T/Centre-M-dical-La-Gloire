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
                                        <b>Laboratoires</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="labo_view" @checked($personnel->hasPermissionTo('labo_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="labo_add" @checked($personnel->hasPermissionTo('labo_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="labo_edit" @checked($personnel->hasPermissionTo('labo_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="labo_delete" @checked($personnel->hasPermissionTo('labo_delete'))> Supprimer
                                    </td>
                                </tr>

                                 <tr>
                                    <td>
                                        <b>Assurances</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="assurance_view" @checked($personnel->hasPermissionTo('assurance_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="assurance_add" @checked($personnel->hasPermissionTo('assurance_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="assurance_edit" @checked($personnel->hasPermissionTo('assurance_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="assurance_delete" @checked($personnel->hasPermissionTo('assurance_delete'))> Supprimer
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Examens</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="examen_view" @checked($personnel->hasPermissionTo('examen_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="examen_add" @checked($personnel->hasPermissionTo('examen_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="examen_edit" @checked($personnel->hasPermissionTo('examen_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="examen_delete" @checked($personnel->hasPermissionTo('examen_delete'))> Supprimer
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Patients</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="patient_view" @checked($personnel->hasPermissionTo('patient_view')) > Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="patient_add" @checked($personnel->hasPermissionTo('patient_add'))> Ajouter
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="patient_edit" @checked($personnel->hasPermissionTo('patient_edit'))> Modifier
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="patient_delete" @checked($personnel->hasPermissionTo('patient_delete'))> Supprimer
                                    </td>
                                    <td colspan="2"></td>
                                </tr>


                                 <tr>
                                    <td>
                                        <b>Visiteurs</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="visiteur_view" @checked($personnel->hasPermissionTo('visiteur_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="visiteur_add" @checked($personnel->hasPermissionTo('visiteur_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="visiteur_edit" @checked($personnel->hasPermissionTo('visiteur_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="visiteur_delete" @checked($personnel->hasPermissionTo('visiteur_delete'))> Supprimer
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <b>Salle d'attente</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_view" @checked($personnel->hasPermissionTo('consultation_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_add" @checked($personnel->hasPermissionTo('consultation_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_edit" @checked($personnel->hasPermissionTo('consultation_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_delete" @checked($personnel->hasPermissionTo('consultation_delete'))> Supprimer
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_edit_champ" @checked($personnel->hasPermissionTo('consultation_edit_champ'))> Editer champs
                                    </td>
                                </tr>

                                 <tr>
                                    <td>
                                        <b>Salle d'attente</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_add_comment" @checked($personnel->hasPermissionTo('consultation_add_comment'))> Ajouter commentaires
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_caisse" @checked($personnel->hasPermissionTo('consultation_caisse'))> Encaisser
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_add_evolution" @checked($personnel->hasPermissionTo('consultation_add_evolution'))> Ajouter évolutions
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_add_exam" @checked($personnel->hasPermissionTo('consultation_add_exam'))>Ajouter examen
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_add_bilan" @checked($personnel->hasPermissionTo('consultation_add_bilan'))> Ajouter bilan
                                    </td>
                                </tr>

                                  <tr>
                                    <td>
                                        <b>Salle d'attente</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_notifications" @checked($personnel->hasPermissionTo('consultation_notifications'))>Voir notifications
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_pay" @checked($personnel->hasPermissionTo('consultation_pay'))> Notification caisse
                                    </td>

                                     <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="consultation_confirm_modif" @checked($personnel->hasPermissionTo('consultation_confirm_modif'))> Marquer vu 
                                    </td>
                                    
                                </tr>

                                 <tr>
                                    <td>
                                        <b>Hospitalisations</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="hospitalisation_view" @checked($personnel->hasPermissionTo('hospitalisation_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="hospitalisation_add" @checked($personnel->hasPermissionTo('hospitalisation_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="hospitalisation_edit" @checked($personnel->hasPermissionTo('hospitalisation_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="hospitalisation_delete" @checked($personnel->hasPermissionTo('hospitalisation_delete'))> Supprimer
                                    </td>
                                </tr>

                                 <tr>
                                    <td>
                                        <b>Dossier médical</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dossier_medical_view" @checked($personnel->hasPermissionTo('dossier_medical_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dossier_medical_add" @checked($personnel->hasPermissionTo('dossier_medical_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dossier_medical_edit" @checked($personnel->hasPermissionTo('dossier_medical_edit'))> Modifier
                                    </td>

                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="dossier_medical_delete" @checked($personnel->hasPermissionTo('dossier_medical_delete'))> Supprimer
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <b>Produits</b>
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
                                        <b>Commandes</b>
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
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="live_order_edit" @checked($personnel->hasPermissionTo('live_order_edit'))>Modifier Status
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="order_delete" @checked($personnel->hasPermissionTo('order_delete'))> Supprimer
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <b>Pharmacie</b>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="boutique_view" @checked($personnel->hasPermissionTo('boutique_view'))> Voir
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="boutique_add" @checked($personnel->hasPermissionTo('boutique_add'))> Ajouter
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="boutique_edit" @checked($personnel->hasPermissionTo('boutique_edit'))> Modifier
                                    </td>


                                    <td>
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="boutique_delete" @checked($personnel->hasPermissionTo('boutique_delete'))> Supprimer
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
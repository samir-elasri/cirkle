    <div class="modal fade" id="modal-confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDelete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title">Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>
                        <strong>Êtes-vous certain de vouloir supprimer cet élément ?</strong>
                    </p>
                    <p id="deletePageName" class="text-danger"></p>
                </div>
                <div class="modal-footer">
                    {!!  Form::open(array('id' => 'deleteForm', 'method' => 'delete')) !!}
                        {!!  Form::button('Annuler', array(
                            'data-dismiss' => 'modal',
                            'class' => 'btn btn-default',
                        )) !!}
                        {!!  Form::submit('Supprimer', array(
                            'class' => 'btn btn-danger',
                        )) !!}
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
	<script>
	        function showConfirmDeleteModal(name, url) {
	            $('#deleteForm').prop('action', url);
	            $('#deletePageName').text(name);
	            $('#modal-confirmDelete').modal({
	                show: true
	            });
	        }
	</script>
<div ng-controller="UploaderCtrl" data-ng-init="initFilename='{{ $obj->$field }}';accept='{{ $options['accept'] }}';entite='{{ ModelUtility::getCollectionNameFromRoute()}}';">
	
	<div class="form-group">
		<label class="col-md-3 control-label">{{ $label }} :</label>
		<div class="col-md-9">
		
			<div data-ng-show="!isDone">
			    <div ngf-drop ngf-select
			    		data-ng-model="files"
			    		class="drop-box"
			    		data-ng-show="!isDone"
			    		ng-model-rejected="rejFiles"
			    		ngf-drag-over-class="{accept:'dragover', reject:'dragreject', delay:100}"
			    		ngf-multiple="true" 
			    		ngf-allow-dir="false"
			        	ngf-accept="validate($file)">

			        <strong>{{ PageUtility::getViewTrans('_admin.widgets.uploader','dropfilehere_single') }}</strong>
			        <div class="alert alert-danger" data-ng-show="messageOn">
						<strong><% message %></strong>
					</div>		        
				    <div ngf-no-file-drop>Le glisser/déposer de fichiers n'est pas pris en charge par ce navigateur</div>
				    
				    <!-- PROGRESS BAR -->
				    <div data-ng-show="progressPercentage > 0 && !isDone" style="margin: 10px 0;">
				        <span data-ng-show="progressPercentage < 100">transfert : <% progressPercentage %>% complété.</span>
				        <span data-ng-show="progressPercentage == 100">S.v.p. attendre , le fichier est en traitement.</span>
				        <div class="progressbar progress-success progressAnimate ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="1"><div class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: <% progressPercentage %>%;"></div></div>
				    </div>				
				</div>
			</div>
			
			<div>
                <div class="input-group">
					<input type="text" id="{{ $field }}" class="form-control" name="{{ $field }}" data-ng-model="filename" ng-readonly="isDone" />
					<div class="input-group-btn" data-ng-show="isDone">
						<button type="button" class="btn btn-primary" data-ng-click="isDone=false">réinitialiser</button>
					</div>
					<div class="input-group-btn" data-ng-show="!isDone">
						<button type="button" class="btn btn-success" data-ng-click="isDone=true"><i class="fa fa-check"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
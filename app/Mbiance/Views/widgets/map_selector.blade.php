 <div ng-controller="MapCtrl" data-ng-cloak
	data-ng-init='latitude={{ number_format($obj->map_latitude,14,'.','') }};longitude={{ number_format($obj->map_longitude,14,'.','') }};zoom_level={{ $obj->map_zoom_level | 0 }};default={{ json_encode($options) }};'>

	<div class="form-group">
		<label class="col-md-3 control-label"></label>
		<div class="col-md-9">
	       <gm-map gm-map-id="'simpleMap'" gm-center="center" gm-zoom="zoom" class="map" style="{{ $options['style'] }}"></gm-map>
	    </div>
	</div>

	<div id="map_latitude_group" class="form-group ">
		<label for="map_latitude" class="col-md-3 control-label">{{ __("validation.attributes.map_latitude") }} :</label>
		<div class="col-md-9">
	       <input id="map_latitude" class="form-control" name="map_latitude" type="number" data-ng-model="centerLat" data-ng-change="updateCenter(centerLat, centerLng)">
	    </div>
	</div>

	<div id="map_longitude_group" class="form-group ">
		<label for="map_longitude" class="col-md-3 control-label">{{ __("validation.attributes.map_longitude") }} :</label>
		<div class="col-md-9">
	       <input id="map_longitude" class="form-control" name="map_longitude" type="number" data-ng-model="centerLng" data-ng-change="updateCenter(centerLat, centerLng)">
	    </div>
	</div>

	<div id="map_zoom_level_group" class="form-group ">
		<label for="map_zoom_level" class="col-md-3 control-label">{{ __("validation.attributes.map_zoom_level") }} :</label>
		<div class="col-md-9">
	       <input id="map_zoom_level" class="form-control" name="map_zoom_level" type="number" data-ng-model="zoom">
	    </div>
	</div>
</div>

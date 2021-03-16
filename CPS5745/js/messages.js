

function alertSuccess(text, id) {
	id = id || "#MessageArea";
	var code = `
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

					${text}
				</div>
			</div>
		</div>
	`;

	$(id).append(code);
}

function alertPrimary(text, id){
	id = id || "#MessageArea";
	var code = `
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-primary alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

					${text}
				</div>
			</div>
		</div>
	`;

	$(id).append(code);
}

function alertDanger(text, id){

	id = id || "#MessageArea";

	var code = `
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

					${text}
				</div>
			</div>
		</div>
	`;

	$(id).append(code);
}
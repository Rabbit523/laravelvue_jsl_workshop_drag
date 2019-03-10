export class Errors {

	constructor() {

		this.errors = {};

	}

	get(field){

		if (this.errors[field]){

			return this.errors[field][0];

		}

	}

	record(errors){

		this.errors = errors;

	}

	has(field){

		return this.errors.hasOwnProperty(field);

	}

	any (){

		return Object.keys(this.errors).length > 0;

	}

	clear(field){

		if(field) {

			delete this.errors[field];

			return;
		}

		this.errors = {};

	}

}


export class Form {

	constructor(data) {

		this.originalData = data;

		for (let field in data ){

			this[field] = data[field];
		}

		this.errors = new Errors();

	}

	reset () {

		for (let field in this.originalData){

			if(this[field].constructor === Array){
				this[field] = [];
			} else {
				this[field] = '';
			}

		}

	}

	data() {

		let data = {};

		for (let property in this.originalData) {
		            data[property] = this[property];
		}

		return data;
    }

	submit(requestType, url, baseurl = null) {

		return new Promise((resolve, reject) => {

            axios[requestType](url, this.data())

                .then(response => {
						
						this.onSuccess(response.data);
						resolve(response.data);
                })
                
                .catch(error => {
						
						this.onFail(error.response.data);
						reject(error.response.data);
						this.isLoading = false;
                });
        
        	});


	}

	onSuccess(data, baseurl = null) {

		if("newid" in data){

			// window.location.href = baseurl + btoa(data.newid) + '/edit';
			// this.reset();

			this.isLoading = false;


		} else {
		
			// alert(data.message);

			this.isLoading = false;

		}


		this.errors.clear();

		

	}

	onFail(errors) {

		//console.log(errors.response.data.errors);
		this.errors.record(errors.errors);

	}

	// Upload Image
	postAvatar(url, fileName, file, oldFileName, oldFile){
    let data = new FormData()

    data.set('fileName', fileName);
    data.set(fileName, file);

    data.set('oldFile', oldFileName);
    data.set(oldFileName, oldFile);


    axios.post(url, data)

            .then(response => {
            	
            	filesrc = fileName + 'image_src';

            	this[filesrc] = asset + response.data.img_src;
            	this[oldFileName] = response.data.img_src;
            	 $( ".fileupload-exists" ).click();
             }) 
             .catch(error => console.log(error));

	}

	

}


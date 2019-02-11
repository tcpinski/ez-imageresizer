class ImageUploader {
  constructor() {
    this.elForm = document.querySelector('#image-uploader');
    this.elFileInput = this.elForm.querySelector('#file_upload');
    this.elSubmit = this.elForm.querySelector('input[type="submit"]');
    this.elFileListImage = this.elForm.querySelector('.image-uploader__list.list--file-image');
    this.elFileListSize = this.elForm.querySelector('.image-uploader__list.list--file-size');
    this.state = {
      files: []
    }
    this.bindEvents();
  }

  bindEvents() {

    this.elForm.addEventListener('submit', (event) => {
      /*
      if (this.state.files.length <= 0) {
        event.preventDefault();
      }
      */
      //event.preventDefault();
      //this.postInput();
    });
    
    this.elFileInput.addEventListener('change', (event) => {

      this.state.files = this.elFileInput.files;

      if (this.validateInput()) {
        this.elSubmit.classList.add('active');

        this.clearLists(this.elFileListImage);
        this.clearLists(this.elFileListSize);
  
        this.createFileList();
        this.elFileListImage.classList.add('active');
        this.elFileListSize.classList.add('active');
      }

    });
  }

  /**
   * Posts the uploaded files to the server. The server will generate the images.
   */
  postInput() {
    const url = '?action_submit';

    let formData = new FormData();

    Array.from(this.state.files).forEach( file => {
      formData.append('file_upload[]', file);
    });

    let request = new XMLHttpRequest();
    request.addEventListener('load', () => {
      console.log(request.response);
    });

    request.open('POST' , url);
    request.send(formData);
  }

  /**
   * Validate the type of file being uploaded. Only allow type: image/*.
   */
  validateInput() {
    let errors = 0;
  
    Array.from(this.state.files).forEach( file => {

      if (!file.type.includes('image')) {
        errors += 1;
      }

    });

    if (errors) return false;
    return true;
  }


  /**
   * Clears all list children from the DOM.
   * @param {elList} list 
   */
  clearLists(elList) {
    Array.from(elList.querySelector('ul').children).forEach( item => {
      item.remove();
    });
  }

  /**
   * Creates a list item for each file in the FileList & adds it to the image uploader list
   */
  createFileList() {
    Array.from(this.state.files).forEach( file => {

      // Get the image dimensions
      const reader = new FileReader();

      reader.readAsDataURL(file);
      reader.onload = (event) => {
        const image = new Image();

        // Set the Base64 string returned from the FileReader as the images source
        image.src = event.target.result;

        image.onload = () => {
          file.width = image.width;
          file.height = image.height;

          let newListItemImage = document.createElement('li');
          let newListItemSize = document.createElement('li');
    
          newListItemImage.innerText = file.name;
          newListItemSize.innerHTML = (file.size / 1024).toPrecision(5) + `<span class="byte-size">kilobytes | ${file.width}px x ${file.height}px</span>`;
    
          this.elFileListImage.querySelector('ul').appendChild(newListItemImage);
          this.elFileListSize.querySelector('ul').appendChild(newListItemSize);
        }
      }
    });

    
  }

}
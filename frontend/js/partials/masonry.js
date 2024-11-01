/**
 * Masonry Layouts.
 */
class Masonry {

	/**
	 * The constructor function.
	 *
	 * @since 1.0.0
	 */
	constructor() {

		this.masonrySelectors = [
			'.wpge-wid-masonry > .gallery',
			'ul.wp-block-gallery.wpge-bl-masonry',
			'.wpge-bl-masonry ul.blocks-gallery-grid',
		];

		this.appplyMasonry();
	}

	/**
	 * Apply Masonry Layout.
	 * 
	 * @since 1.0.0
	 */
	appplyMasonry() {

		let elems = document.querySelectorAll(this.masonrySelectors.join( ',' ));
		let elemsArray = Array.prototype.slice.call(elems);
		elemsArray.forEach(elem => {
			this.createGrid(elem);
		});
	}

	/**
	 * Create Grid Layout.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 */
	createGrid(elem) {

		let options = this.getOptions(elem);
		let magicgrid = new brickLayer(options);
		magicgrid.init();
	}

	/**
	 * Get Masonry Layout Options.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 */
	getOptions(elem) {

		let options = {
			container: elem,
			gutter: 0,
			waitForImages: true,
			useTransform: false,
		};

		if (elem.closest('.wp-block-gallery')) {
			options.itemSelector = '.blocks-gallery-item';
			options.callBefore = this.addImageOrientation.bind(this, elem);
			options.callAfter = this.addLoadedClass.bind(this, elem);
		}

		return options;
	}

	/**
	 * Add portrait/landscape class to the block galleries.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 */
	addImageOrientation(elem) {
		let images;
		if (elem.closest('wp-block-gallery')) {
			images = elem.getElementsByTagName('img');
			images = Array.prototype.slice.call(images);
			images.forEach(image => {
				if ( image.naturalWidth > image.naturalHeight ) {
					image.parentElement.parentElement.classList.add('landscape');
				} else {
					image.parentElement.parentElement.classList.add('portrait');
				}
			});
		}
	}

	/**
	 * Add display class to the container.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 */
	addLoadedClass(elem) {
		elem.classList.add('gloaded');
	}
}

export default Masonry;
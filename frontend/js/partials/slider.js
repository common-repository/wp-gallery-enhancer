/**
 * Slider Functionality.
 */
class Slider {

	/**
	 * The constructor function.
	 *
	 * @since 1.0.0
	 */
	constructor() {

		this.sliderSelectors = [
			'.wpge-wid-slider > .gallery',
			'ul.wp-block-gallery.wpge-bl-slider',
			'.wpge-bl-slider ul.blocks-gallery-grid',
		];

		this.sliderFunctionality();
	}

	/**
	 * Init Slider Functionality.
	 * 
	 * @since 1.0.0
	 */
	sliderFunctionality() {

		let elems = document.querySelectorAll(this.sliderSelectors.join( ',' ));
		let elemsArray = Array.prototype.slice.call(elems);
		
		elemsArray.forEach(elem => {
			let options = {
				cellAlign: 'center',
				contain: true,
				wrapAround: true,
				prevNextButtons: true,
				imagesLoaded: true,
				cellSelector: '.blocks-gallery-item, .gallery-item',
			};
			new Flickity(elem, options);
		});
	}
}

export default Slider;
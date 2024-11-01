/**
 * Gallery Layouts.
 */
class Gallery {

	/**
	 * The constructor function.
	 *
	 * @since 1.0.0
	 */
	constructor(gallery) {

		this.masonCls  = [ '.wpge-bl-masonry' ];
		this.slidercls = [ '.wpge-bl-slider' ];
		this.gallery = gallery;
		this.timeStamp = null;

		this.init();
	}

	/**
	 * Initialize Gallery.
	 * 
	 * @since 1.0.0
	 */
	init() {
		if (this.isMason(this.gallery)) {
			this.createGrid(this.gallery, true);
		} else if (this.isSlider(this.gallery)) {
			this.createSlider(this.gallery, true);
		}
	}

	/**
	 * Create Grid Layout.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 * @param {bool} observe
	 */
	createGrid(elem, observe) {

		let options = {
			container: elem,
			itemSelector: '.blocks-gallery-item',
			gutter: 0,
			waitForImages: true,
			dynamicContent: true,
			callBefore: this.addImageOrientation.bind(this, elem),
			callAfter: this.addLoadedClass.bind(this, elem),
			runBefore: 'always',
		};
		let magicgrid = new brickLayer(options);
		magicgrid.init();

		if (observe) {
			this.observeChanges(elem, magicgrid, false);
		} else {
			return magicgrid;
		}
	}

	/**
	 * Create Slider.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} elem
	 * @param {bool} observe
	 */
	createSlider(elem, observe) {
		const items = elem.getElementsByClassName('blocks-gallery-item');
		const itemArr = Array.prototype.slice.call(items);
		const options = {
			cellAlign: 'center',
			contain: true,
			wrapAround: true,
			prevNextButtons: true,
			imagesLoaded: true,
			cellSelector: '.slider-gallery-items',
		};

		itemArr.forEach(item => {
			this.appendItem(item, elem, false);
		});

		let flkty = new Flickity(elem, options);
		if (observe) {
			this.observeChanges(elem, false, flkty);
		} else {
			return flkty;
		}
	}

	/**
	 * Onserve Gallery element for changes.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} el
	 * @param {object} magicgrid
	 * @param {object} flkty
	 */
	observeChanges(el, magicgrid, flkty) {
		const elem = el.closest('.wp-block-gallery');
		const config = { attributes: true, attributeFilter: ['class'], childList: true };
		let callback = (mutationsList, observer) => {
			mutationsList.forEach(mutation => {
				const elem = el;
				if ('attributes' == mutation.type) {
					const isMason  = this.isMason(elem);
					const isSlider = this.isSlider(elem)
					if (!isMason) {
						if (false !== magicgrid) {
							magicgrid.destroy();
							magicgrid = false;
						}
					}

					if (!isSlider) {
						if (false !== flkty) {
							this.removeSlider(elem, flkty);
							flkty = false;
						}
					}
						
					if (isMason) {
						if (!magicgrid) {
							magicgrid = this.createGrid(elem, false);
						} else {
							magicgrid.setup();
						}
					}

					if (isSlider) {
						if (!flkty) {
							flkty = this.createSlider(elem, false);
						} else {
							flkty.resize();
						}
					}
				} else if (mutation.type == 'childList') {
					if (flkty) {
						const addedNodes = Array.prototype.slice.call(mutation.addedNodes);
						const removedNodes = Array.prototype.slice.call(mutation.removedNodes);
						addedNodes.forEach(node => {
							if (1 === node.nodeType && node.classList.contains('blocks-gallery-item')) {
								this.appendItem(node, elem, flkty)
							}
						});
						removedNodes.forEach(rnode => {
							const children = flkty.getCellElements();
							if (1 === rnode.nodeType) {
								if ( rnode.classList.contains('has-add-item-button')) {
									children.forEach(child => {
										if (child.classList.contains('has-add-item-button')) {
											flkty.remove(child);
										}
									});
								} else {
									const img = rnode.querySelector('img');
									const src = img ? img.src : false;
									if (src && flkty.slider) {
										const cimg = flkty.slider.querySelector('img[src="' + src + '"]');
										const child = cimg ? cimg.parentElement.parentElement : '';
										if (child) {
											flkty.remove(child);
										}
									}
								}
							}
						});
					}
				}
			});
		}
		let observer = new MutationObserver(callback);
		if (el && !el.classList.contains('wp-block-gallery')) observer.observe(el, config);
		if (elem) observer.observe(elem, config);
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
		if (elem.closest('.wp-block-gallery')) {
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

	/**
	 * Checkif current element contains Masonry class.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} elem
	 */
	isMason(elem) {
		const isMason = this.masonCls.filter(selector => elem.closest(selector) );
		return isMason.length ? true : false;
	}

	/**
	 * Checkif current element contains slider class.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} elem
	 */
	isSlider(elem) {
		const isSlider = this.slidercls.filter(selector => elem.closest(selector) );
		return isSlider.length ? true : false;
	}

	/**
	 * Append item to the gallery.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} item
	 * @param {object} elem
	 * @param {object} flkty
	 */
	appendItem(item, elem, flkty) {
		let clone = item.cloneNode(true)
		item.style.display = "none";
		clone.classList.add('slider-gallery-items');
		if (flkty) {
			flkty.append(clone);
		} else {
			elem.appendChild(clone);
		}
	}

	/**
	 * Create Slider.
	 * 
	 * @since 1.0.0
	 *
	 * @param {object} elem
	 * @param {object} flkty
	 */
	removeSlider(elem, flkty) {
		const items = elem.getElementsByClassName('blocks-gallery-item');
		const itemArr = Array.prototype.slice.call(items);
		const children = flkty.getCellElements();

		flkty.destroy();
		children.forEach(child => {
			elem.removeChild(child);
		});
		itemArr.forEach(item => {
			item.style.display = "block";
		});
	}
}

export default Gallery;
/**
 * External Dependencies
 */
import classnames from 'classnames';

/**
 * Internal Dependencies
 */
import GalleryStyle from './gallery.js';

/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Component, Fragment, createRef } = wp.element;
const { SelectControl } = wp.components;
const { InspectorAdvancedControls } = wp.blockEditor;
const { createHigherOrderComponent } = wp.compose;

/**
 * Add custom attributes to gallery blocks
 *
 * @param {function|Component} BlockEdit Original component.
 * @return {string} Wrapped component.
 */
const withAdvancedControls = createHigherOrderComponent( ( BlockEdit ) => {
	return class extends Component {
		constructor(props) {
			super(props);
			this.isGallery = 'core/gallery' === props.name;
			this.galRef = createRef();
			this.galObj = false;
			this.initTimeOut = null;
		}

		componentDidMount() {
			if ( ! this.isGallery ) return;
			this.initGalleryStyling();
		}

		componentDidUpdate(prevProps) {
			if ( ! this.isGallery ) return;

			if (false === this.galObj) {
				this.initGalleryStyling();
			}
		}

		initGalleryStyling() {
			clearTimeout(this.initTimeOut);
			this.initTimeOut = setTimeout( () => {
				const wrapper = this.galRef.current;
				const gallery = wrapper.querySelector('ul.wp-block-gallery, ul.blocks-gallery-grid');
				if (null !== gallery && this.isWpge(gallery)) {
					this.galObj = new GalleryStyle(gallery);
				}
			}, 100 );
		}

		isWpge(gallery) {
			const wpgeClasses = [ '.wpge-bl-masonry', '.wpge-bl-slider' ];
			const wpge = wpgeClasses.filter(selector => gallery.closest(selector) );
			return wpge.length ? true : false;
		}

		render() {
			const { attributes, setAttributes, isSelected } = this.props;
			const {className, imageCrop} = attributes;
			const galleryStyling = [ 'wpge-bl-slider', 'wpge-bl-masonry' ];
			const cropSize = [ 'port1', 'port2', 'land1', 'widescrn', 'sqre' ];
			const cropPos = [ 'topcrop' ];

			const onChange = (value, selection) => {
				let names = className || '';
				if ('' !== names) {
					let classArr = names.split(' ');
					classArr = classArr.filter(val => !selection.includes(val));
					names = classArr.join(' ');
				}
				if ('' === names && '' === value) {
					setAttributes({ className: undefined });
				} else {
					setAttributes({ className: classnames( names, value ) });
				}
			}

			const getValue = selection => {
				let names = className || '';
				if ('' !== names) {
					let classArr = names.split(' ');
					classArr = classArr.filter(val => selection.includes(val));
					names = classArr.length ? classArr[0] : '';
				}
				return names;
			}

			if ( ! this.isGallery ) {
				return (
					<BlockEdit { ...this.props } />
				);
			}

			return (
				<Fragment>
					{ isSelected &&
						<InspectorAdvancedControls>
							<SelectControl
								label={ __( 'Gallery Display Style', 'wp-gallery-enhancer' ) }
								value={ getValue(galleryStyling) }
								onChange={ (value) => onChange(value, galleryStyling) }
								options={ [
									{ value: '', label: __( '- Default -', 'wp-gallery-enhancer' ) },
									{ value: 'wpge-bl-slider', label: __( 'Slider', 'wp-gallery-enhancer' ) },
									{ value: 'wpge-bl-masonry', label: __( 'Masonry', 'wp-gallery-enhancer' ) },
								] }
							/>
							{
								!!imageCrop &&
								<SelectControl
									label={ __( 'Cropping Method', 'wp-gallery-enhancer' ) }
									value={ getValue(cropSize) }
									onChange={ (value) => onChange(value, cropSize) }
									options={ [
										{ value: '', label: __( 'Landscape (3:2)', 'wp-gallery-enhancer' ) },
										{ value: 'port1', label: __( 'Portrait (3:4)', 'wp-gallery-enhancer' ) },
										{ value: 'port2', label: __( 'Portrait (2:3)', 'wp-gallery-enhancer' ) },
										{ value: 'land1', label: __( 'Landscape (4:3)', 'wp-gallery-enhancer' ) },
										{ value: 'widescrn', label: __( 'WideScreen (16:9)', 'wp-gallery-enhancer' ) },
										{ value: 'sqre', label: __( 'Square (1:1)', 'wp-gallery-enhancer' ) },
									] }
								/>
							}
							{
								!!imageCrop &&
								<SelectControl
									label={ __( 'Cropping Position', 'wp-gallery-enhancer' ) }
									value={ getValue(cropPos) }
									onChange={ (value) => onChange(value, cropPos) }
									options={ [
										{ value: '', label: __( 'Center Crop', 'wp-gallery-enhancer' ) },
										{ value: 'topcrop', label: __( 'Top Left Crop', 'wp-gallery-enhancer' ) },
									] }
								/>
							}
						</InspectorAdvancedControls>
					}
					<div className="wpge-block-gallery" ref={this.galRef}>
						<BlockEdit { ...this.props }/>
					</div>
				</Fragment>
			);
		}
	};
}, 'withAdvancedControls' );

addFilter(
	'editor.BlockEdit',
	'wpge/with-advanced-controls',
	withAdvancedControls
);

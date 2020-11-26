/**
 * External dependencies
 */
import PropTypes from 'prop-types';

function IconSVG( { hasBadge } ) {
	return (
		<svg className={ `amp-toolbar-icon${ hasBadge ? ' amp-toolbar-icon--has-badge' : '' }` } width="21" height="21" viewBox="0 0 21 21" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fillRule="evenodd" clipRule="evenodd" d="M0.377197 10.6953C0.377197 16.1953 4.8772 20.6953 10.3772 20.6953C15.8772 20.6953 20.3772 16.1953 20.3772 10.6953C20.3772 5.19531 15.8772 0.695312 10.3772 0.695312C4.8772 0.695312 0.377197 5.19531 0.377197 10.6953Z" />
			<path d="M9.5772 16.7953H8.8772L9.6772 12.2953H7.3772C7.1772 12.2953 6.9772 12.0953 6.9772 11.8953C6.9772 11.7953 7.0772 11.6953 7.0772 11.6953L11.2772 4.69531H12.0772L11.2772 9.29531H13.5772C13.7772 9.29531 13.9772 9.49531 13.9772 9.69531C13.9772 9.79531 13.9772 9.89531 13.8772 9.89531L9.5772 16.7953ZM10.3772 0.695312C4.8772 0.695312 0.377197 5.19531 0.377197 10.6953C0.377197 16.1953 4.8772 20.6953 10.3772 20.6953C15.8772 20.6953 20.3772 16.1953 20.3772 10.6953C20.3772 5.19531 15.8772 0.695312 10.3772 0.695312Z" fill="white" />
		</svg>
	);
}
IconSVG.propTypes = {
	hasBadge: PropTypes.bool.isRequired,
};

export function BrokenIconSVG( { hasBadge } ) {
	return (
		<svg className={ `amp-toolbar-broken-icon${ hasBadge ? ' amp-toolbar-broken-icon--has-badge' : '' }` } width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fillRule="evenodd" clipRule="evenodd" d="M0.345627 10.7207C0.345627 16.2207 4.84563 20.7207 10.3456 20.7207C15.8456 20.7207 20.3456 16.2207 20.3456 10.7207C20.3456 5.2207 15.8456 0.720703 10.3456 0.720703C4.84563 0.720703 0.345627 5.2207 0.345627 10.7207Z" fill="white" />
			<path d="M9.54563 16.8207H8.84563L9.64563 12.3207H7.34563C7.14563 12.3207 6.94563 12.1207 6.94563 11.9207C6.94563 11.8207 7.04563 11.7207 7.04563 11.7207L11.2456 4.7207H12.0456L11.2456 9.3207H13.5456C13.7456 9.3207 13.9456 9.5207 13.9456 9.7207C13.9456 9.8207 13.9456 9.9207 13.8456 9.9207L9.54563 16.8207ZM10.3456 0.720703C4.84563 0.720703 0.345627 5.2207 0.345627 10.7207C0.345627 16.2207 4.84563 20.7207 10.3456 20.7207C15.8456 20.7207 20.3456 16.2207 20.3456 10.7207C20.3456 5.2207 15.8456 0.720703 10.3456 0.720703Z" fill="#37414B" />
			<circle cx="10.3456" cy="10.7207" r="9" stroke="#BB522E" strokeWidth="2" />
			<line x1="15.9518" y1="17.7833" x2="3.22383" y2="5.05536" stroke="#BB522E" strokeWidth="2" />
			<line x1="19.2379" y1="18.5552" x2="2.71443" y2="1.68601" stroke="white" strokeWidth="2" />
		</svg>
	);
}
BrokenIconSVG.propTypes = {
	hasBadge: PropTypes.bool.isRequired,
};

export function ToolbarIcon( { broken = false, count } ) {
	return (
		<div className={ `amp-plugin-icon ${ broken ? 'amp-plugin-icon--broken' : '' }` }>
			{
				broken ? <BrokenIconSVG hasBadge={ Boolean( count ) } /> : <IconSVG hasBadge={ Boolean( count ) } />
			}
			{ 0 < count && (
				<div className="amp-error-count-badge">
					{ count }
				</div>
			) }
		</div>
	);
}
ToolbarIcon.propTypes = {
	broken: PropTypes.bool,
	count: PropTypes.number.isRequired,
};

export function MoreMenuIcon() {
	return <IconSVG hasBadge={ false } />;
}

export function NewTabIcon() {
	return (
		<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M15.8968 16.1732H4.23014V4.50651H10.0635V2.83984H4.23014C3.30514 2.83984 2.56348 3.58984 2.56348 4.50651V16.1732C2.56348 17.0898 3.30514 17.8398 4.23014 17.8398H15.8968C16.8135 17.8398 17.5635 17.0898 17.5635 16.1732V10.3398H15.8968V16.1732ZM11.7301 2.83984V4.50651H14.7218L6.53014 12.6982L7.70514 13.8732L15.8968 5.68151V8.67318H17.5635V2.83984H11.7301Z" fill="black" />
		</svg>
	);
}

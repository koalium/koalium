import { applyFilters } from '@wordpress/hooks';

export const noStyleAttributes = applyFilters(
	'generateblocks.utils.noStyleAttributes',
	[
		'accordionItemOpen',
		'accordionMultipleOpen',
		'alt',
		'anchor',
		'ariaLabel',
		'blockLabel',
		'blockVersion',
		'buttonType',
		'className',
		'columns',
		'content',
		'cssClasses',
		'customIcon',
		'dateReplacePublished',
		'dateType',
		'defaultOpenedTab',
		'dynamicContentType',
		'dynamicLinkType',
		'dynamicSource',
		'element',
		'elementId',
		'globalClasses',
		'globalStyleId',
		'globalStyleLabel',
		'gridId',
		'hasIcon',
		'hasUrl',
		'hiddenLinkAriaLabel',
		'href',
		'htmlAttributes',
		'icon',
		'iconLocation',
		'isDynamic',
		'isDynamic',
		'isGlobalStyle',
		'isGrid',
		'isPagination',
		'isQueryLoop',
		'isQueryLoopItem',
		'linkMetaFieldName',
		'linkType',
		'lock',
		'marginSyncUnits',
		'mediaId',
		'mediaUrl',
		'metaFieldName',
		'multipleCommentsText',
		'noCommentsText',
		'openInNewWindow',
		'paddingSyncUnits',
		'postId',
		'postType',
		'query',
		'relNoFollow',
		'relSponsored',
		'singleCommentsText',
		'sizeSlug',
		'tabItemOpen',
		'tagName',
		'target',
		'termSeparator',
		'termTaxonomy',
		'text',
		'title',
		'uniqueId',
		'url',
		'useDynamicData',
		'useGlobalStyle',
		'useInnerContainer',
		'variantRole',
	],
);

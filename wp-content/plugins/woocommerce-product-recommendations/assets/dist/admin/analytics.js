/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 694:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__(925);

function emptyFunction() {}
function emptyFunctionWithReset() {}
emptyFunctionWithReset.resetWarningCache = emptyFunction;

module.exports = function() {
  function shim(props, propName, componentName, location, propFullName, secret) {
    if (secret === ReactPropTypesSecret) {
      // It is still safe when called from React.
      return;
    }
    var err = new Error(
      'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
      'Use PropTypes.checkPropTypes() to call them. ' +
      'Read more at http://fb.me/use-check-prop-types'
    );
    err.name = 'Invariant Violation';
    throw err;
  };
  shim.isRequired = shim;
  function getShim() {
    return shim;
  };
  // Important!
  // Keep this list in sync with production version in `./factoryWithTypeCheckers.js`.
  var ReactPropTypes = {
    array: shim,
    bigint: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    elementType: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim,

    checkPropTypes: emptyFunctionWithReset,
    resetWarningCache: emptyFunction
  };

  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ 556:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__(694)();
}


/***/ }),

/***/ 925:
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";

;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external "React"
const external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
// EXTERNAL MODULE: ./node_modules/prop-types/index.js
var prop_types = __webpack_require__(556);
var prop_types_default = /*#__PURE__*/__webpack_require__.n(prop_types);
;// CONCATENATED MODULE: external ["wc","components"]
const external_wc_components_namespaceObject = window["wc"]["components"];
;// CONCATENATED MODULE: external ["wc","data"]
const external_wc_data_namespaceObject = window["wc"]["data"];
;// CONCATENATED MODULE: external ["wc","wcSettings"]
const external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
;// CONCATENATED MODULE: external "lodash"
const external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wc","navigation"]
const external_wc_navigation_namespaceObject = window["wc"]["navigation"];
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/lib/index.js
/**
 * External dependencies.
 */





/**
 * Exports.
 */
function getRequestByIdString(path, handleData = identity) {
  return function (queryString = '') {
    const pathString = path;
    const idList = (0,external_wc_navigation_namespaceObject.getIdsFromQuery)(queryString);
    if (idList.length < 1) {
      return Promise.resolve([]);
    }
    const payload = {
      include: idList.join(','),
      per_page: idList.length
    };
    return external_wp_apiFetch_default()({
      path: (0,external_wp_url_namespaceObject.addQueryArgs)(pathString, payload)
    }).then(data => data.map(handleData));
  };
}

/**
 * Takes a chart name returns the configuration for that chart from and array
 * of charts. If the chart is not found it will return the first chart.
 *
 * @param {string} chartName - the name of the chart to get configuration for
 * @param {Array} charts - list of charts for a particular report
 * @return {Object} - chart configuration object
 */
function getSelectedChart(chartName, charts = []) {
  const chart = (0,external_lodash_namespaceObject.find)(charts, {
    key: chartName
  });
  if (chart) {
    return chart;
  }
  return charts[0];
}
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/config.js
/**
 * External dependencies.
 */


/**
 * WooCommerce dependencies.
 */



/**
 * SomewhereWarm dependencies.
 */


/**
 * Helpers.
 */
const getProductLabels = getRequestByIdString(external_wc_data_namespaceObject.NAMESPACE + '/products', product => ({
  key: product.id,
  label: product.name
}));
const getLocationOptions = () => {
  const adminSettings = (0,external_wc_wcSettings_namespaceObject.getSetting)('admin', {});
  const prlLocationOptions = adminSettings?.prlLocationOptions || [];
  return prlLocationOptions;
};

/**
 * Exports.
 */
const advancedFilters = {
  title: (0,external_wp_i18n_namespaceObject.__)(
  // A sentence describing filters for Orders
  // See screen shot for context: https://cloudup.com/cSsUY9VeCVJ
  // 'Orders Match {{select /}} Filters',
  'Filter by Product and Location', 'woocommerce-product-recommendations'),
  filters: {
    location: {
      allowMultiple: true,
      labels: {
        add: (0,external_wp_i18n_namespaceObject.__)('Location', 'woocommerce-product-recommendations'),
        remove: (0,external_wp_i18n_namespaceObject.__)('Remove Location filter', 'woocommerce-product-recommendations'),
        rule: (0,external_wp_i18n_namespaceObject.__)('Choose a Location', 'woocommerce-product-recommendations'),
        title: (0,external_wp_i18n_namespaceObject.__)('Location {{rule /}} {{filter /}}', 'woocommerce-product-recommendations'),
        filter: (0,external_wp_i18n_namespaceObject.__)('Select a Location', 'woocommerce-product-recommendations')
      },
      rules: [{
        value: 'includes',
        label: (0,external_wp_i18n_namespaceObject._x)('Is', 'location-status-report-filter', 'woocommerce-product-recommendations')
      }, {
        value: 'excludes',
        label: (0,external_wp_i18n_namespaceObject._x)('Is not', 'location-status-report-filter', 'woocommerce-product-recommendations')
      }],
      input: {
        component: 'SelectControl',
        options: getLocationOptions()
      }
    },
    product: {
      labels: {
        add: (0,external_wp_i18n_namespaceObject.__)('Products', 'woocommerce-product-recommendations'),
        placeholder: (0,external_wp_i18n_namespaceObject.__)('Search products', 'woocommerce-product-recommendations'),
        remove: (0,external_wp_i18n_namespaceObject.__)('Remove Products filter', 'woocommerce-product-recommendations'),
        rule: (0,external_wp_i18n_namespaceObject.__)('Choose Products', 'woocommerce-product-recommendations'),
        /* translators: A sentence describing a Product filter. See screen shot for context: https://cloudup.com/cSsUY9VeCVJ */
        title: (0,external_wp_i18n_namespaceObject.__)('{{title}}Product{{/title}} {{rule /}} {{filter /}}', 'woocommerce-product-recommendations'),
        filter: (0,external_wp_i18n_namespaceObject.__)('Select Products', 'woocommerce-product-recommendations')
      },
      rules: [{
        value: 'includes',
        /* translators: Sentence fragment, logical, "Includes" refers to orders including a given product(s). Screenshot for context: https://cloudup.com/cSsUY9VeCVJ */
        label: (0,external_wp_i18n_namespaceObject._x)('Includes', 'products-report-filter', 'woocommerce-product-recommendations')
      }, {
        value: 'excludes',
        /* translators: Sentence fragment, logical, "Excludes" refers to orders excluding a given product(s). Screenshot for context: https://cloudup.com/cSsUY9VeCVJ */
        label: (0,external_wp_i18n_namespaceObject._x)('Excludes', 'products-report-filter', 'woocommerce-product-recommendations')
      }],
      input: {
        component: 'Search',
        type: 'products',
        getLabels: getProductLabels
      }
    }
  }
};
const filters = [{
  label: (0,external_wp_i18n_namespaceObject.__)('Show', 'woocommerce-product-recommendations'),
  staticParams: ['section', 'paged', 'per_page'],
  param: 'filter',
  showFilters: () => true,
  filters: [{
    label: (0,external_wp_i18n_namespaceObject.__)('All revenue', 'woocommerce-product-recommendations'),
    value: 'all'
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Advanced filters', 'woocommerce-product-recommendations'),
    value: 'advanced'
  }]
}];
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/revenue/config.js
/**
 * External dependencies.
 */


/**
 * Exports.
 */
const ENDPOINT = 'recommendations-revenue';
const charts = [{
  key: 'gross_sales',
  label: (0,external_wp_i18n_namespaceObject.__)('Gross sales', 'woocommerce-product-recommendations'),
  order: 'desc',
  orderby: 'gross_sales',
  type: 'currency'
}, {
  key: 'net_sales',
  label: (0,external_wp_i18n_namespaceObject.__)('Net sales', 'woocommerce-product-recommendations'),
  order: 'desc',
  orderby: 'net_sales',
  type: 'currency'
}, {
  key: 'items_sold',
  label: (0,external_wp_i18n_namespaceObject.__)('Items sold', 'woocommerce-product-recommendations'),
  order: 'desc',
  orderby: 'items_sold',
  type: 'number'
}, {
  key: 'orders_count',
  label: (0,external_wp_i18n_namespaceObject.__)('Orders', 'woocommerce-product-recommendations'),
  order: 'desc',
  orderby: 'orders_count',
  type: 'number'
}];
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","date"]
const external_wp_date_namespaceObject = window["wp"]["date"];
;// CONCATENATED MODULE: external ["wc","number"]
const external_wc_number_namespaceObject = window["wc"]["number"];
;// CONCATENATED MODULE: external ["wc","date"]
const external_wc_date_namespaceObject = window["wc"]["date"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// CONCATENATED MODULE: external ["wc","csvExport"]
const external_wc_csvExport_namespaceObject = window["wc"]["csvExport"];
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/download-icon.js

/* harmony default export */ const download_icon = (() => (0,external_React_namespaceObject.createElement)("svg", {
  role: "img",
  "aria-hidden": "true",
  focusable: "false",
  version: "1.1",
  xmlns: "http://www.w3.org/2000/svg",
  x: "0px",
  y: "0px",
  viewBox: "0 0 24 24"
}, (0,external_React_namespaceObject.createElement)("path", {
  d: "M18,9c-0.009,0-0.017,0.002-0.025,0.003C17.72,5.646,14.922,3,11.5,3C7.91,3,5,5.91,5,9.5c0,0.524,0.069,1.031,0.186,1.519 C5.123,11.016,5.064,11,5,11c-2.209,0-4,1.791-4,4c0,1.202,0.541,2.267,1.38,3h18.593C22.196,17.089,23,15.643,23,14 C23,11.239,20.761,9,18,9z M12,16l-4-5h3V8h2v3h3L12,16z"
})));
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-error/index.js

/**
 * External dependencies
 */






/**
 * Component to render when there is an error in a report component due to data
 * not being loaded or being invalid.
 */
class report_error_ReportError extends external_wp_element_namespaceObject.Component {
  render() {
    const {
      className,
      isError,
      isEmpty
    } = this.props;
    let title, actionLabel, actionURL, actionCallback;
    if (isError) {
      title = (0,external_wp_i18n_namespaceObject.__)('There was an error getting your stats. Please try again.', 'woocommerce-product-bundles');
      actionLabel = (0,external_wp_i18n_namespaceObject.__)('Reload', 'woocommerce-product-bundles');
      actionCallback = () => {
        window.location.reload();
      };
    } else if (isEmpty) {
      title = (0,external_wp_i18n_namespaceObject.__)('No results could be found for this date range.', 'woocommerce-product-bundles');
      actionLabel = (0,external_wp_i18n_namespaceObject.__)('View Orders', 'woocommerce-product-bundles');
      actionURL = (0,external_wc_wcSettings_namespaceObject.getAdminLink)('edit.php?post_type=shop_order');
    }
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.EmptyContent, {
      className: className,
      title: title,
      actionLabel: actionLabel,
      actionURL: actionURL,
      actionCallback: actionCallback
    });
  }
}
report_error_ReportError.propTypes = {
  /**
   * Additional class name to style the component.
   */
  className: (prop_types_default()).string,
  /**
   * Boolean representing whether there was an error.
   */
  isError: (prop_types_default()).bool,
  /**
   * Boolean representing whether the issue is that there is no data.
   */
  isEmpty: (prop_types_default()).bool
};
report_error_ReportError.defaultProps = {
  className: ''
};
/* harmony default export */ const report_error = (report_error_ReportError);
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/utils.js
/**
 * External dependencies
 */

function extendTableData(select, props, queriedTableData) {
  const {
    extendItemsMethodNames,
    extendedItemsStoreName,
    itemIdField
  } = props;
  const itemsData = queriedTableData.items.data;
  if (!Array.isArray(itemsData) || !itemsData.length || !extendItemsMethodNames || !itemIdField) {
    return queriedTableData;
  }
  const {
    [extendItemsMethodNames.getError]: getErrorMethod,
    [extendItemsMethodNames.isRequesting]: isRequestingMethod,
    [extendItemsMethodNames.load]: loadMethod
  } = select(extendedItemsStoreName);
  const extendQuery = {
    include: itemsData.map(item => item[itemIdField]).join(','),
    per_page: itemsData.length
  };
  const extendedItems = loadMethod(extendQuery);
  const isExtendedItemsRequesting = isRequestingMethod ? isRequestingMethod(extendQuery) : false;
  const isExtendedItemsError = getErrorMethod ? getErrorMethod(extendQuery) : false;
  const extendedItemsData = itemsData.map(item => {
    const extendedItemData = first(extendedItems.filter(extendedItem => item.id === extendedItem.id));
    return {
      ...item,
      ...extendedItemData
    };
  });
  const isRequesting = queriedTableData.isRequesting || isExtendedItemsRequesting;
  const isError = queriedTableData.isError || isExtendedItemsError;
  return {
    ...queriedTableData,
    isRequesting,
    isError,
    items: {
      ...queriedTableData.items,
      data: extendedItemsData
    }
  };
}
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/index.js

/**
 * External dependencies
 */














/**
 * Internal dependencies
 */



const TABLE_FILTER = 'woocommerce_admin_report_table';
const ReportTable = props => {
  const {
    getHeadersContent,
    getRowsContent,
    getSummary,
    isRequesting,
    primaryData,
    tableData,
    endpoint,
    // These props are not used in the render function, but are destructured
    // so they are not included in the `tableProps` variable.
    // eslint-disable-next-line no-unused-vars
    itemIdField,
    // eslint-disable-next-line no-unused-vars
    tableQuery,
    compareBy,
    compareParam,
    searchBy,
    labels = {},
    ...tableProps
  } = props;

  // Pull these props out separately because they need to be included in tableProps.
  const {
    query,
    columnPrefsKey
  } = props;
  const {
    items,
    query: reportQuery
  } = tableData;
  const initialSelectedRows = query[compareParam] ? (0,external_wc_navigation_namespaceObject.getIdsFromQuery)(query[compareBy]) : [];
  const [selectedRows, setSelectedRows] = (0,external_wp_element_namespaceObject.useState)(initialSelectedRows);
  const scrollPointRef = (0,external_wp_element_namespaceObject.useRef)(null);
  const {
    updateUserPreferences,
    ...userData
  } = (0,external_wc_data_namespaceObject.useUserPreferences)();

  // Bail early if we've encountered an error.
  const isError = tableData.isError || primaryData.isError;
  if (isError) {
    return (0,external_React_namespaceObject.createElement)(report_error, {
      isError: true
    });
  }
  let userPrefColumns = [];
  if (columnPrefsKey) {
    userPrefColumns = userData && userData[columnPrefsKey] ? userData[columnPrefsKey] : userPrefColumns;
  }
  const onPageChange = (newPage, source) => {
    scrollPointRef.current.scrollIntoView();
    const tableElement = scrollPointRef.current.nextSibling.querySelector('.woocommerce-table__table');
    const focusableElements = external_wp_dom_namespaceObject.focus.focusable.find(tableElement);
    if (focusableElements.length) {
      focusableElements[0].focus();
    }
  };
  const onSort = (key, direction) => {
    (0,external_wc_navigation_namespaceObject.onQueryChange)('sort')(key, direction);
    const eventProps = {
      report: endpoint,
      column: key,
      direction
    };
  };
  const filterShownHeaders = (headers, hiddenKeys) => {
    // If no user preferences, set visibilty based on column default.
    if (!hiddenKeys) {
      return headers.map(header => ({
        ...header,
        visible: header.required || !header.hiddenByDefault
      }));
    }

    // Set visibilty based on user preferences.
    return headers.map(header => ({
      ...header,
      visible: header.required || !hiddenKeys.includes(header.key)
    }));
  };
  const applyTableFilters = (data, totals, totalResults) => {
    const summary = getSummary ? getSummary(totals, totalResults) : null;

    /**
     * Filter report table for the CSV download.
     *
     * Enables manipulation of data used to create the report CSV.
     *
     * @param {Object} reportTableData - data used to create the table.
     * @param {string} reportTableData.endpoint - table api endpoint.
     * @param {Array} reportTableData.headers - table headers data.
     * @param {Array} reportTableData.rows - table rows data.
     * @param {Object} reportTableData.totals - total aggregates for request.
     * @param {Array} reportTableData.summary - summary numbers data.
     * @param {Object} reportTableData.items - response from api requerst.
     */
    return (0,external_wp_hooks_namespaceObject.applyFilters)(TABLE_FILTER, {
      endpoint,
      headers: getHeadersContent(),
      rows: getRowsContent(data),
      totals,
      summary,
      items
    });
  };
  const onClickDownload = () => {
    const {
      createNotice,
      startExport,
      title
    } = props;
    const params = Object.assign({}, query);
    const {
      data,
      totalResults
    } = items;
    let downloadType = 'browser';

    // Delete unnecessary items from filename.
    delete params.extended_info;
    if (params.search) {
      delete params[searchBy];
    }
    if (data && data.length === totalResults) {
      const {
        headers,
        rows
      } = applyTableFilters(data, totalResults);
      (0,external_wc_csvExport_namespaceObject.downloadCSVFile)((0,external_wc_csvExport_namespaceObject.generateCSVFileName)(title, params), (0,external_wc_csvExport_namespaceObject.generateCSVDataFromTable)(headers, rows));
    } else {
      downloadType = 'email';
      startExport(endpoint, reportQuery).then(() => createNotice('success', (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s = type of report */
      (0,external_wp_i18n_namespaceObject.__)('Your %s Report will be emailed to you.', 'woocommerce-admin'), title))).catch(error => createNotice('error', error.message || (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s = type of report */
      (0,external_wp_i18n_namespaceObject.__)('There was a problem exporting your %s Report. Please try again.', 'woocommerce-admin'), title)));
    }
  };
  const onCompare = () => {
    if (compareBy) {
      (0,external_wc_navigation_namespaceObject.onQueryChange)('compare')(compareBy, compareParam, selectedRows.join(','));
    }
  };
  const onSearchChange = values => {
    const {
      baseSearchQuery
    } = props;
    // A comma is used as a separator between search terms, so we want to escape
    // any comma they contain.
    const searchTerms = values.map(v => v.label.replace(',', '%2C'));
    if (searchTerms.length) {
      (0,external_wc_navigation_namespaceObject.updateQueryString)({
        filter: undefined,
        [compareParam]: undefined,
        [searchBy]: undefined,
        ...baseSearchQuery,
        search: (0,external_lodash_namespaceObject.uniq)(searchTerms).join(',')
      });
    } else {
      (0,external_wc_navigation_namespaceObject.updateQueryString)({
        search: undefined
      });
    }
  };
  const selectAllRows = checked => {
    const {
      ids
    } = props;
    setSelectedRows(checked ? ids : []);
  };
  const selectRow = (i, checked) => {
    const {
      ids
    } = props;
    if (checked) {
      setSelectedRows((0,external_lodash_namespaceObject.uniq)([ids[i], ...selectedRows]));
    } else {
      const index = selectedRows.indexOf(ids[i]);
      setSelectedRows([...selectedRows.slice(0, index), ...selectedRows.slice(index + 1)]);
    }
  };
  const getCheckbox = i => {
    const {
      ids = []
    } = props;
    const isChecked = selectedRows.indexOf(ids[i]) !== -1;
    return {
      display: (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
        onChange: (0,external_lodash_namespaceObject.partial)(selectRow, i),
        checked: isChecked
      }),
      value: false
    };
  };
  const getAllCheckbox = () => {
    const {
      ids = []
    } = props;
    const hasData = ids.length > 0;
    const isAllChecked = hasData && ids.length === selectedRows.length;
    return {
      cellClassName: 'is-checkbox-column',
      key: 'compare',
      label: (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
        onChange: selectAllRows,
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Select All'),
        checked: isAllChecked,
        disabled: !hasData
      }),
      required: true
    };
  };
  const isLoading = isRequesting || tableData.isRequesting || primaryData.isRequesting;
  const totals = (0,external_lodash_namespaceObject.get)(primaryData, ['data', 'totals'], {});
  const totalResults = items.totalResults || 0;
  const downloadable = totalResults > 0;
  // Search words are in the query string, not the table query.
  const searchWords = (0,external_wc_navigation_namespaceObject.getSearchWords)(query);
  const searchedLabels = searchWords.map(v => ({
    key: v,
    label: v
  }));
  const {
    data
  } = items;
  const applyTableFiltersResult = applyTableFilters(data, totals, totalResults);
  let {
    headers,
    rows
  } = applyTableFiltersResult;
  const {
    summary
  } = applyTableFiltersResult;
  const onColumnsChange = (shownColumns, toggledColumn) => {
    const columns = headers.map(header => header.key);
    const hiddenColumns = columns.filter(column => !shownColumns.includes(column));
    if (columnPrefsKey) {
      const userDataFields = {
        [columnPrefsKey]: hiddenColumns
      };
      updateUserPreferences(userDataFields);
    }
  };

  // Add in selection for comparisons.
  if (compareBy) {
    rows = rows.map((row, i) => {
      return [getCheckbox(i), ...row];
    });
    headers = [getAllCheckbox(), ...headers];
  }

  // Hide any headers based on user prefs, if loaded.
  const filteredHeaders = filterShownHeaders(headers, userPrefColumns);
  return (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)("div", {
    className: "woocommerce-report-table__scroll-point",
    ref: scrollPointRef,
    "aria-hidden": true
  }), (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.TableCard, {
    className: ('woocommerce-report-table', 'woocommerce-report-table-' + endpoint.replace('/', '-')),
    hasSearch: !!searchBy,
    actions: [compareBy && (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.CompareButton, {
      key: "compare",
      className: "woocommerce-table__compare",
      count: selectedRows.length,
      helpText: labels.helpText || (0,external_wp_i18n_namespaceObject.__)('Check at least two items below to compare', 'woocommerce-admin'),
      onClick: onCompare,
      disabled: !downloadable
    }, labels.compareButton || (0,external_wp_i18n_namespaceObject.__)('Compare', 'woocommerce-admin')), searchBy && (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Search, {
      allowFreeTextSearch: true,
      inlineTags: true,
      key: "search",
      onChange: onSearchChange,
      placeholder: labels.placeholder || (0,external_wp_i18n_namespaceObject.__)('Search by item name', 'woocommerce-admin'),
      selected: searchedLabels,
      showClearButton: true,
      type: searchBy,
      disabled: !downloadable
    }), downloadable && (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      key: "download",
      className: "woocommerce-table__download-button",
      disabled: isLoading,
      onClick: onClickDownload
    }, (0,external_React_namespaceObject.createElement)(download_icon, null), (0,external_React_namespaceObject.createElement)("span", {
      className: "woocommerce-table__download-button__label"
    }, labels.downloadButton || (0,external_wp_i18n_namespaceObject.__)('Download', 'woocommerce-admin')))],
    headers: filteredHeaders,
    isLoading: isLoading,
    onQueryChange: external_wc_navigation_namespaceObject.onQueryChange,
    onColumnsChange: onColumnsChange,
    onSort: onSort,
    onPageChange: onPageChange,
    rows: rows,
    rowsPerPage: parseInt(reportQuery.per_page, 10) || external_wc_data_namespaceObject.QUERY_DEFAULTS.pageSize,
    summary: summary,
    totalRows: totalResults,
    ...tableProps
  }));
};
ReportTable.propTypes = {
  /**
   * Pass in query parameters to be included in the path when onSearch creates a new url.
   */
  baseSearchQuery: (prop_types_default()).object,
  /**
   * The string to use as a query parameter when comparing row items.
   */
  compareBy: (prop_types_default()).string,
  /**
   * Url query parameter compare function operates on
   */
  compareParam: (prop_types_default()).string,
  /**
   * The key for user preferences settings for column visibility.
   */
  columnPrefsKey: (prop_types_default()).string,
  /**
   * The endpoint to use in API calls to populate the table rows and summary.
   * For example, if `taxes` is provided, data will be fetched from the report
   * `taxes` endpoint (ie: `/wc-analytics/reports/taxes` and `/wc/v4/reports/taxes/stats`).
   * If the provided endpoint doesn't exist, an error will be shown to the user
   * with `ReportError`.
   */
  endpoint: (prop_types_default()).string,
  /**
   * A function that returns the headers object to build the table.
   */
  getHeadersContent: (prop_types_default()).func.isRequired,
  /**
   * A function that returns the rows array to build the table.
   */
  getRowsContent: (prop_types_default()).func.isRequired,
  /**
   * A function that returns the summary object to build the table.
   */
  getSummary: (prop_types_default()).func,
  /**
   * The name of the property in the item object which contains the id.
   */
  itemIdField: (prop_types_default()).string,
  /**
   * Custom labels for table header actions.
   */
  labels: prop_types_default().shape({
    compareButton: (prop_types_default()).string,
    downloadButton: (prop_types_default()).string,
    helpText: (prop_types_default()).string,
    placeholder: (prop_types_default()).string
  }),
  /**
   * Primary data of that report. If it's not provided, it will be automatically
   * loaded via the provided `endpoint`.
   */
  primaryData: (prop_types_default()).object,
  /**
   * The string to use as a query parameter when searching row items.
   */
  searchBy: (prop_types_default()).string,
  /**
   * List of fields used for summary numbers. (Reduces queries)
   */
  summaryFields: prop_types_default().arrayOf((prop_types_default()).string),
  /**
   * Table data of that report. If it's not provided, it will be automatically
   * loaded via the provided `endpoint`.
   */
  tableData: (prop_types_default()).object.isRequired,
  /**
   * Properties to be added to the query sent to the report table endpoint.
   */
  tableQuery: (prop_types_default()).object,
  /**
   * String to display as the title of the table.
   */
  title: (prop_types_default()).string.isRequired
};
ReportTable.defaultProps = {
  primaryData: {},
  tableData: {
    items: {
      data: [],
      totalResults: 0
    },
    query: {}
  },
  tableQuery: {},
  compareParam: 'filter',
  downloadable: false,
  onSearch: external_lodash_namespaceObject.noop,
  baseSearchQuery: {}
};
const EMPTY_ARRAY = (/* unused pure expression or super */ null && ([]));
const EMPTY_OBJECT = {};
/* harmony default export */ const report_table = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    endpoint,
    getSummary,
    isRequesting,
    itemIdField,
    query,
    tableData,
    tableQuery,
    filters,
    advancedFilters,
    summaryFields,
    extendedItemsStoreName
  } = props;
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');
  if (isRequesting) {
    return EMPTY_OBJECT;
  }

  /* eslint @wordpress/no-unused-vars-before-return: "off" */
  const reportStoreSelector = select(external_wc_data_namespaceObject.REPORTS_STORE_NAME);
  const extendedStoreSelector = extendedItemsStoreName ? select(extendedItemsStoreName) : null;
  const primaryData = getSummary ? (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint: endpoint,
    dataType: 'primary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    filters,
    advancedFilters,
    defaultDateRange,
    fields: summaryFields
  }) : EMPTY_OBJECT;
  const queriedTableData = tableData || (0,external_wc_data_namespaceObject.getReportTableData)({
    endpoint,
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    tableQuery,
    filters,
    advancedFilters,
    defaultDateRange
  });
  return {
    primaryData,
    tableData: queriedTableData,
    query
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    startExport
  } = dispatch(external_wc_data_namespaceObject.EXPORT_STORE_NAME);
  const {
    createNotice
  } = dispatch('core/notices');
  return {
    createNotice,
    startExport
  };
}))(ReportTable));
;// CONCATENATED MODULE: external ["wc","currency"]
const external_wc_currency_namespaceObject = window["wc"]["currency"];
var external_wc_currency_default = /*#__PURE__*/__webpack_require__.n(external_wc_currency_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/lib/currency-context.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

const appCurrency = external_wc_currency_default()(external_wc_wcSettings_namespaceObject.CURRENCY);
const getFilteredCurrencyInstance = query => {
  const config = appCurrency.getCurrencyConfig();
  const filteredConfig = applyFilters('woocommerce_admin_report_currency', config, query);
  return CurrencyFactory(filteredConfig);
};
const CurrencyContext = (0,external_wp_element_namespaceObject.createContext)(appCurrency // default value
);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/revenue/table.js

/**
 * External dependencies.
 */













/**
 * SomewhereWarm dependencies.
 */



/**
 * Internal dependencies.
 */

class RevenueReportTable extends external_wp_element_namespaceObject.Component {
  constructor() {
    super();
    this.getHeadersContent = this.getHeadersContent.bind(this);
    this.getRowsContent = this.getRowsContent.bind(this);
    this.getSummary = this.getSummary.bind(this);
  }
  getHeadersContent() {
    return [{
      label: (0,external_wp_i18n_namespaceObject.__)('Date', 'woocommerce-product-recommendations'),
      key: 'date',
      required: true,
      isLeftAligned: true,
      isSortable: false
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Items sold', 'woocommerce-product-recommendations'),
      key: 'items_sold',
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Gross sales', 'woocommerce-product-recommendations'),
      key: 'gross_sales',
      defaultSort: true,
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Net sales', 'woocommerce-product-recommendations'),
      key: 'net_sales',
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Orders', 'woocommerce-product-recommendations'),
      key: 'orders_count',
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Products', 'woocommerce-product-recommendations'),
      key: 'products_count',
      isSortable: true,
      isNumeric: true
    }].filter(Boolean);
  }
  getRowsContent(data = []) {
    const {
      query
    } = this.props;
    const persistedQuery = (0,external_wc_navigation_namespaceObject.getPersistedQuery)(query);
    const admin = (0,external_wc_wcSettings_namespaceObject.getSetting)('admin', {});
    const dateFormat = admin.dateFormat ? admin.dateFormat : external_wc_date_namespaceObject.defaultTableDateFormat;
    const {
      render: renderCurrency,
      formatDecimal: getCurrencyFormatDecimal,
      getCurrencyConfig
    } = this.context;
    const currency = getCurrencyConfig();
    return data.map(row => {
      const {
        gross_sales: grossSales,
        net_sales: netSales,
        items_sold: itemsSold,
        orders_count: ordersCount,
        products_count: productsCount
      } = row.subtotals;
      return [{
        display: (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Date, {
          date: row.date_start,
          visibleFormat: dateFormat
        }),
        value: row.date_start
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', itemsSold),
        // ? itemsSold : '0',
        value: Number(itemsSold)
      }, {
        display: renderCurrency(grossSales),
        value: getCurrencyFormatDecimal(grossSales)
      }, {
        display: renderCurrency(netSales),
        value: getCurrencyFormatDecimal(netSales)
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', ordersCount),
        value: Number(ordersCount)
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', productsCount),
        value: Number(productsCount)
      }];
    });
  }
  getSummary(totals) {
    const {
      orders_count: ordersCount = 0,
      products_count: productsCount = 0,
      items_sold: itemsSold = 0,
      net_sales: netSales = 0,
      gross_sales: grossSales = 0
    } = totals;
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    const currency = getCurrencyConfig();
    return [{
      label: (0,external_wp_i18n_namespaceObject._n)('item sold', 'items sold', itemsSold, 'woocommerce-product-recommendations'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', itemsSold)
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('gross', 'woocommerce-product-recommendations'),
      value: formatAmount(grossSales)
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('net', 'woocommerce-product-recommendations'),
      value: formatAmount(netSales)
    }, {
      label: (0,external_wp_i18n_namespaceObject._n)('product', 'products', productsCount, 'woocommerce-product-recommendations'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', productsCount)
    }, {
      label: (0,external_wp_i18n_namespaceObject._n)('order', 'orders', ordersCount, 'woocommerce-product-recommendations'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', ordersCount)
    }];
  }
  render() {
    const {
      filters,
      advancedFilters,
      isRequesting,
      hideCompare,
      query,
      tableData,
      endpoint
    } = this.props;
    return (0,external_React_namespaceObject.createElement)(report_table, {
      endpoint: endpoint,
      getHeadersContent: this.getHeadersContent,
      getRowsContent: this.getRowsContent,
      getSummary: this.getSummary,
      summaryFields: ['orders_count', 'products_count', 'items_sold', 'net_sales', 'gross_sales'],
      isRequesting: isRequesting,
      query: query,
      tableData: tableData,
      columnPrefsKey: "prl_revenue_report_columns",
      compareBy: hideCompare ? undefined : 'recommendations',
      title: (0,external_wp_i18n_namespaceObject.__)('Revenue', 'woocommerce-product-recommendations'),
      filters: filters,
      advancedFilters: advancedFilters
    });
  }
}
RevenueReportTable.contextType = CurrencyContext;
const table_EMPTY_ARRAY = [];
const table_EMPTY_OBJECT = {};

/**
 * Memoized props object formatting function.
 *
 * @param {boolean} isError
 * @param {boolean} isRequesting
 * @param {Object}  tableQuery
 * @param {Object}  revenueData
 * @return {Object} formatted tableData prop
 */
const formatProps = (isError, isRequesting, tableQuery, revenueData) => ({
  tableData: {
    items: {
      data: (0,external_lodash_namespaceObject.get)(revenueData, ['data', 'intervals'], table_EMPTY_ARRAY),
      totalResults: (0,external_lodash_namespaceObject.get)(revenueData, ['totalResults'], 0)
    },
    isError,
    isRequesting,
    query: tableQuery
  }
});

/**
 * Memoized table query formatting function.
 *
 * @param {string} order
 * @param {string} orderBy
 * @param {number} page
 * @param {number} pageSize
 * @param {Object} datesFromQuery
 * @return {Object} formatted tableQuery object
 */
const formatTableQuery = (0,external_lodash_namespaceObject.memoize)(
// @todo Support hour here when viewing a single day
(order, orderBy, page, pageSize, datesFromQuery) => ({
  interval: 'day',
  orderby: orderBy,
  order,
  page,
  per_page: pageSize,
  after: (0,external_wc_date_namespaceObject.appendTimestamp)(datesFromQuery.primary.after, 'start'),
  before: (0,external_wc_date_namespaceObject.appendTimestamp)(datesFromQuery.primary.before, 'end')
}), (order, orderBy, page, pageSize, datesFromQuery) => [order, orderBy, page, pageSize, datesFromQuery.primary.after, datesFromQuery.primary.before].join(':'));
/* harmony default export */ const table = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    query,
    filters,
    advancedFilters
  } = props;
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');
  const datesFromQuery = (0,external_wc_date_namespaceObject.getCurrentDates)(query, defaultDateRange);
  const {
    getReportStats,
    getReportStatsError,
    isResolving
  } = select(external_wc_data_namespaceObject.REPORTS_STORE_NAME);
  const tableQuery = formatTableQuery(query.order || 'desc', query.orderby || 'date', query.paged || 1, query.per_page || external_wc_data_namespaceObject.QUERY_DEFAULTS.pageSize, datesFromQuery);
  const filteredTableQuery = (0,external_wc_data_namespaceObject.getReportTableQuery)({
    endpoint: ENDPOINT,
    query,
    select,
    tableQuery,
    filters,
    advancedFilters
  });
  const revenueData = getReportStats(ENDPOINT, filteredTableQuery);
  const isError = Boolean(getReportStatsError(ENDPOINT, filteredTableQuery));
  const isRequesting = isResolving('getReportStats', [ENDPOINT, filteredTableQuery]);
  return formatProps(isError, isRequesting, tableQuery, revenueData);
}))(RevenueReportTable));
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-summary/index.js

/**
 * External dependencies
 */






/**
 * WooCommerce dependencies
 */






/**
 * Internal dependencies
 */
// import ReportError from '../report-error';


/**
 * Component to render summary numbers in reports.
 */
class ReportSummary extends external_wp_element_namespaceObject.Component {
  formatVal(val, type) {
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    return type === 'currency' ? formatAmount(val) : (0,external_wc_number_namespaceObject.formatValue)(getCurrencyConfig(), type, val);
  }
  getValues(key, type) {
    const {
      emptySearchResults,
      summaryData
    } = this.props;
    const {
      totals
    } = summaryData;
    const primaryTotal = totals.primary ? totals.primary[key] : 0;
    const secondaryTotal = totals.secondary ? totals.secondary[key] : 0;
    const primaryValue = emptySearchResults ? 0 : primaryTotal;
    const secondaryValue = emptySearchResults ? 0 : secondaryTotal;
    return {
      delta: (0,external_wc_number_namespaceObject.calculateDelta)(primaryValue, secondaryValue),
      prevValue: this.formatVal(secondaryValue, type),
      value: this.formatVal(primaryValue, type)
    };
  }
  render() {
    const {
      charts,
      query,
      selectedChart,
      summaryData,
      endpoint,
      report,
      defaultDateRange
    } = this.props;
    const {
      isError,
      isRequesting
    } = summaryData;
    if (isError) {
      // return <ReportError isError />;
      return;
    }
    if (isRequesting) {
      return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryListPlaceholder, {
        numberOfItems: charts.length
      });
    }
    const {
      compare
    } = (0,external_wc_date_namespaceObject.getDateParamsFromQuery)(query, defaultDateRange);
    const renderSummaryNumbers = ({
      onToggle
    }) => charts.map(chart => {
      const {
        key,
        order,
        orderby,
        label,
        type
      } = chart;
      const newPath = {
        chart: key
      };
      if (orderby) {
        newPath.orderby = orderby;
      }
      if (order) {
        newPath.order = order;
      }
      const href = (0,external_wc_navigation_namespaceObject.getNewPath)(newPath);
      const isSelected = selectedChart.key === key;
      const {
        delta,
        prevValue,
        value
      } = this.getValues(key, type);
      return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryNumber, {
        key: key,
        delta: delta,
        href: href,
        label: label,
        prevLabel: compare === 'previous_period' ? (0,external_wp_i18n_namespaceObject.__)('Previous Period:', 'woocommerce-product-bundles') : (0,external_wp_i18n_namespaceObject.__)('Previous Year:', 'woocommerce-product-bundles'),
        prevValue: prevValue,
        selected: isSelected,
        value: value,
        onLinkClickCallback: () => {
          // Wider than a certain breakpoint, there is no dropdown so avoid calling onToggle.
          if (onToggle) {
            onToggle();
          }
        }
      });
    });
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryList, null, renderSummaryNumbers);
  }
}
ReportSummary.propTypes = {
  /**
   * Properties of all the charts available for that report.
   */
  charts: (prop_types_default()).array.isRequired,
  /**
   * The endpoint to use in API calls to populate the Summary Numbers.
   * For example, if `taxes` is provided, data will be fetched from the report
   * `taxes` endpoint (ie: `/wc-analytics/reports/taxes/stats`). If the provided endpoint
   * doesn't exist, an error will be shown to the user with `ReportError`.
   */
  endpoint: (prop_types_default()).string.isRequired,
  /**
   * The query string represented in object form.
   */
  query: (prop_types_default()).object.isRequired,
  /**
   * Properties of the selected chart.
   */
  selectedChart: prop_types_default().shape({
    /**
     * Key of the selected chart.
     */
    key: (prop_types_default()).string.isRequired,
    /**
     * Chart label.
     */
    label: (prop_types_default()).string.isRequired,
    /**
     * Order query argument.
     */
    order: prop_types_default().oneOf(['asc', 'desc']),
    /**
     * Order by query argument.
     */
    orderby: (prop_types_default()).string,
    /**
     * Number type for formatting.
     */
    type: prop_types_default().oneOf(['average', 'number', 'currency']).isRequired
  }).isRequired,
  /**
   * Data to display in the SummaryNumbers.
   */
  summaryData: (prop_types_default()).object,
  /**
   * Report name, if different than the endpoint.
   */
  report: (prop_types_default()).string
};
ReportSummary.defaultProps = {
  summaryData: {
    totals: {
      primary: {},
      secondary: {}
    },
    isError: false
  }
};
ReportSummary.contextType = CurrencyContext;
/* harmony default export */ const report_summary = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    charts,
    endpoint,
    limitProperties,
    query,
    filters,
    advancedFilters
  } = props;
  const limitBy = limitProperties || [endpoint];
  const hasLimitByParam = limitBy.some(item => query[item] && query[item].length);
  if (query.search && !hasLimitByParam) {
    return {
      emptySearchResults: true
    };
  }
  const fields = charts && charts.map(chart => chart.key);
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');
  const summaryData = (0,external_wc_data_namespaceObject.getSummaryNumbers)({
    endpoint,
    query,
    select,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  return {
    summaryData,
    defaultDateRange
  };
}))(ReportSummary));
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-chart/utils.js
/**
 * External dependencies
 */


const DEFAULT_FILTER = 'all';
function getSelectedFilter(filters, query, selectedFilterArgs = {}) {
  if (!filters || filters.length === 0) {
    return null;
  }
  const clonedFilters = filters.slice(0);
  const filterConfig = clonedFilters.pop();
  if (filterConfig.showFilters(query, selectedFilterArgs)) {
    const allFilters = (0,external_wc_navigation_namespaceObject.flattenFilters)(filterConfig.filters);
    const value = query[filterConfig.param] || filterConfig.defaultValue || DEFAULT_FILTER;
    return (0,external_lodash_namespaceObject.find)(allFilters, {
      value
    });
  }
  return getSelectedFilter(clonedFilters, query, selectedFilterArgs);
}
function getChartMode(selectedFilter, query) {
  if (selectedFilter && query) {
    const selectedFilterParam = (0,external_lodash_namespaceObject.get)(selectedFilter, ['settings', 'param']);
    if (!selectedFilterParam || Object.keys(query).includes(selectedFilterParam)) {
      return (0,external_lodash_namespaceObject.get)(selectedFilter, ['chartMode']);
    }
  }
  return null;
}
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-chart/index.js

/**
 * External dependencies
 */








/**
 * WooCommerce dependencies
 */




/**
 * Internal dependencies
 */




/**
 * Component that renders the chart in reports.
 */
class ReportChart extends external_wp_element_namespaceObject.Component {
  shouldComponentUpdate(nextProps) {
    if (nextProps.isRequesting !== this.props.isRequesting || nextProps.primaryData.isRequesting !== this.props.primaryData.isRequesting || nextProps.secondaryData.isRequesting !== this.props.secondaryData.isRequesting || !(0,external_lodash_namespaceObject.isEqual)(nextProps.query, this.props.query)) {
      return true;
    }
    return false;
  }
  getItemChartData() {
    const {
      primaryData,
      selectedChart
    } = this.props;
    const chartData = primaryData.data.intervals.map(function (interval) {
      const intervalData = {};
      interval.subtotals.segments.forEach(function (segment) {
        if (segment.segment_label) {
          const label = intervalData[segment.segment_label] ? segment.segment_label + ' (#' + segment.segment_id + ')' : segment.segment_label;
          intervalData[segment.segment_id] = {
            label,
            value: segment.subtotals[selectedChart.key] || 0
          };
        }
      });
      return {
        date: (0,external_wp_date_namespaceObject.format)('Y-m-d\\TH:i:s', interval.date_start),
        ...intervalData
      };
    });
    return chartData;
  }
  getTimeChartData() {
    const {
      query,
      primaryData,
      secondaryData,
      selectedChart,
      defaultDateRange
    } = this.props;
    const currentInterval = (0,external_wc_date_namespaceObject.getIntervalForQuery)(query);
    const {
      primary,
      secondary
    } = (0,external_wc_date_namespaceObject.getCurrentDates)(query, defaultDateRange);
    const chartData = primaryData.data.intervals.map(function (interval, index) {
      const secondaryDate = (0,external_wc_date_namespaceObject.getPreviousDate)(interval.date_start, primary.after, secondary.after, query.compare, currentInterval);
      const secondaryInterval = secondaryData.data.intervals[index];
      return {
        date: (0,external_wp_date_namespaceObject.format)('Y-m-d\\TH:i:s', interval.date_start),
        primary: {
          label: `${primary.label} (${primary.range})`,
          labelDate: interval.date_start,
          value: interval.subtotals[selectedChart.key] || 0
        },
        secondary: {
          label: `${secondary.label} (${secondary.range})`,
          labelDate: secondaryDate.format('YYYY-MM-DD HH:mm:ss'),
          value: secondaryInterval && secondaryInterval.subtotals[selectedChart.key] || 0
        }
      };
    });
    return chartData;
  }
  getTimeChartTotals() {
    const {
      primaryData,
      secondaryData,
      selectedChart
    } = this.props;
    return {
      primary: (0,external_lodash_namespaceObject.get)(primaryData, ['data', 'totals', selectedChart.key], null),
      secondary: (0,external_lodash_namespaceObject.get)(secondaryData, ['data', 'totals', selectedChart.key], null)
    };
  }
  renderChart(mode, isRequesting, chartData, legendTotals) {
    const {
      emptySearchResults,
      filterParam,
      interactiveLegend,
      itemsLabel,
      legendPosition,
      path,
      query,
      selectedChart,
      showHeaderControls,
      primaryData
    } = this.props;
    const currentInterval = (0,external_wc_date_namespaceObject.getIntervalForQuery)(query);
    const allowedIntervals = (0,external_wc_date_namespaceObject.getAllowedIntervalsForQuery)(query);
    const formats = (0,external_wc_date_namespaceObject.getDateFormatsForInterval)(currentInterval, primaryData.data.intervals.length);
    const emptyMessage = emptySearchResults ? (0,external_wp_i18n_namespaceObject.__)('No data for the current search', 'woocommerce-admin') : (0,external_wp_i18n_namespaceObject.__)('No data for the selected date range', 'woocommerce-admin');
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Chart, {
      allowedIntervals: allowedIntervals,
      data: chartData,
      dateParser: '%Y-%m-%dT%H:%M:%S',
      emptyMessage: emptyMessage,
      filterParam: filterParam,
      interactiveLegend: interactiveLegend,
      interval: currentInterval,
      isRequesting: isRequesting,
      itemsLabel: itemsLabel,
      legendPosition: legendPosition,
      legendTotals: legendTotals,
      mode: mode,
      path: path,
      query: query,
      screenReaderFormat: formats.screenReaderFormat,
      showHeaderControls: showHeaderControls,
      title: selectedChart.label,
      tooltipLabelFormat: formats.tooltipLabelFormat,
      tooltipTitle: mode === 'time-comparison' && selectedChart.label || null,
      tooltipValueFormat: (0,external_wc_data_namespaceObject.getTooltipValueFormat)(selectedChart.type, formatAmount),
      chartType: (0,external_wc_date_namespaceObject.getChartTypeForQuery)(query),
      valueType: selectedChart.type,
      xFormat: formats.xFormat,
      x2Format: formats.x2Format,
      currency: getCurrencyConfig()
    });
  }
  renderItemComparison() {
    const {
      isRequesting,
      primaryData
    } = this.props;
    if (primaryData.isError) {
      return (0,external_React_namespaceObject.createElement)(report_error, {
        isError: true
      });
    }
    const isChartRequesting = isRequesting || primaryData.isRequesting;
    const chartData = this.getItemChartData();
    return this.renderChart('item-comparison', isChartRequesting, chartData);
  }
  renderTimeComparison() {
    const {
      isRequesting,
      primaryData,
      secondaryData
    } = this.props;
    if (!primaryData || primaryData.isError || secondaryData.isError) {
      return (0,external_React_namespaceObject.createElement)(report_error, {
        isError: true
      });
    }
    const isChartRequesting = isRequesting || primaryData.isRequesting || secondaryData.isRequesting;
    const chartData = this.getTimeChartData();
    const legendTotals = this.getTimeChartTotals();
    return this.renderChart('time-comparison', isChartRequesting, chartData, legendTotals);
  }
  render() {
    const {
      mode
    } = this.props;
    if (mode === 'item-comparison') {
      return this.renderItemComparison();
    }
    return this.renderTimeComparison();
  }
}
ReportChart.contextType = CurrencyContext;
ReportChart.propTypes = {
  /**
   * Filters available for that report.
   */
  filters: (prop_types_default()).array,
  /**
   * Whether there is an API call running.
   */
  isRequesting: (prop_types_default()).bool,
  /**
   * Label describing the legend items.
   */
  itemsLabel: (prop_types_default()).string,
  /**
   * Allows specifying properties different from the `endpoint` that will be used
   * to limit the items when there is an active search.
   */
  limitProperties: (prop_types_default()).array,
  /**
   * `items-comparison` (default) or `time-comparison`, this is used to generate correct
   * ARIA properties.
   */
  mode: (prop_types_default()).string,
  /**
   * Current path
   */
  path: (prop_types_default()).string.isRequired,
  /**
   * Primary data to display in the chart.
   */
  primaryData: (prop_types_default()).object,
  /**
   * The query string represented in object form.
   */
  query: (prop_types_default()).object.isRequired,
  /**
   * Secondary data to display in the chart.
   */
  secondaryData: (prop_types_default()).object,
  /**
   * Properties of the selected chart.
   */
  selectedChart: prop_types_default().shape({
    /**
     * Key of the selected chart.
     */
    key: (prop_types_default()).string.isRequired,
    /**
     * Chart label.
     */
    label: (prop_types_default()).string.isRequired,
    /**
     * Order query argument.
     */
    order: prop_types_default().oneOf(['asc', 'desc']),
    /**
     * Order by query argument.
     */
    orderby: (prop_types_default()).string,
    /**
     * Number type for formatting.
     */
    type: prop_types_default().oneOf(['average', 'number', 'currency']).isRequired
  }).isRequired
};
ReportChart.defaultProps = {
  isRequesting: false,
  primaryData: {
    data: {
      intervals: []
    },
    isError: false,
    isRequesting: false
  },
  secondaryData: {
    data: {
      intervals: []
    },
    isError: false,
    isRequesting: false
  }
};
/* harmony default export */ const report_chart = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    charts,
    endpoint,
    filters,
    isRequesting,
    limitProperties,
    query,
    advancedFilters
  } = props;
  const limitBy = limitProperties || [endpoint];
  const selectedFilter = getSelectedFilter(filters, query);
  const filterParam = (0,external_lodash_namespaceObject.get)(selectedFilter, ['settings', 'param']);
  const chartMode = props.mode || getChartMode(selectedFilter, query) || 'time-comparison';
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');

  /* eslint @wordpress/no-unused-vars-before-return: "off" */
  const reportStoreSelector = select(external_wc_data_namespaceObject.REPORTS_STORE_NAME);
  const newProps = {
    mode: chartMode,
    filterParam,
    defaultDateRange
  };
  if (isRequesting) {
    return newProps;
  }
  const hasLimitByParam = limitBy.some(item => query[item] && query[item].length);
  if (query.search && !hasLimitByParam) {
    return {
      ...newProps,
      emptySearchResults: true
    };
  }
  const fields = charts && charts.map(chart => chart.key);
  const primaryData = (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint,
    dataType: 'primary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  if (chartMode === 'item-comparison') {
    return {
      ...newProps,
      primaryData
    };
  }
  const secondaryData = (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint,
    dataType: 'secondary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  return {
    ...newProps,
    primaryData,
    secondaryData
  };
}))(ReportChart));
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/revenue/index.js

/**
 * External dependencies.
 */




/**
 * WooCommerce dependencies.
 */


/**
 * Internal dependencies.
 */





/**
 * SomewhereWarm dependencies.
 */



class RevenueReport extends external_wp_element_namespaceObject.Component {
  getChartMeta() {
    const {
      query,
      isSingleProductView
    } = this.props;
    const mode = 'time-comparison';
    const compareObject = 'recommendations';
    /* translators: Number of conversions */
    const label = (0,external_wp_i18n_namespaceObject.__)('%d conversions', 'woocommerce-product-recommendations');
    return {
      itemsLabel: label,
      mode
    };
  }
  render() {
    const {
      itemsLabel,
      mode
    } = this.getChartMeta();
    const {
      path,
      query,
      isError,
      isRequesting
    } = this.props;
    if (isError) {
      return (0,external_React_namespaceObject.createElement)(ReportError, {
        isError: true
      });
    }
    const chartQuery = {
      ...query
    };
    return (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.ReportFilters, {
      query: query,
      path: path,
      showDatePicker: true,
      filters: filters,
      advancedFilters: advancedFilters
    }), (0,external_React_namespaceObject.createElement)(report_summary, {
      mode: mode,
      charts: charts,
      endpoint: ENDPOINT,
      isRequesting: isRequesting,
      query: chartQuery,
      selectedChart: getSelectedChart(query.chart, charts),
      filters: filters,
      advancedFilters: advancedFilters
    }), (0,external_React_namespaceObject.createElement)(report_chart, {
      charts: charts,
      mode: mode,
      endpoint: ENDPOINT,
      isRequesting: isRequesting,
      itemsLabel: itemsLabel,
      path: path,
      query: chartQuery,
      selectedChart: getSelectedChart(chartQuery.chart, charts),
      filters: filters,
      advancedFilters: advancedFilters
    }), (0,external_React_namespaceObject.createElement)(table, {
      isRequesting: isRequesting,
      endpoint: ENDPOINT,
      hideCompare: true,
      query: query,
      filters: filters,
      advancedFilters: advancedFilters
    }));
  }
}
RevenueReport.propTypes = {
  path: (prop_types_default()).string.isRequired,
  query: (prop_types_default()).object.isRequired
};
/* harmony default export */ const revenue = (RevenueReport);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/index.js

/**
 * Internal dependencies.
 */


/**
 * SomewhereWarm dependencies.
 */

const Report = props => {
  const {
    query
  } = props;
  props.query.section = 'revenue';
  let main_content;
  switch (query.section) {
    case 'revenue':
      main_content = (0,external_React_namespaceObject.createElement)(revenue, {
        ...props
      });
      break;
    default:
      main_content = (0,external_React_namespaceObject.createElement)(report_error, null);
  }
  return main_content;
};
/* harmony default export */ const report = (Report);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/index.js
/**
 * External dependencies
 */




/**
 * Local imports
 */


/**
 * Use the 'woocommerce_admin_reports_list' filter to add a report page.
 */
(0,external_wp_hooks_namespaceObject.addFilter)('woocommerce_admin_reports_list', 'woocommerce-product-recommendations', reports => {
  return [...reports, {
    report: 'recommendations',
    title: (0,external_wp_i18n_namespaceObject._x)('Recommendations', 'analytics report menu item', 'woocommerce-product-recommendations'),
    component: report,
    navArgs: {
      id: 'wc-prl-recommendations-analytics-report'
    }
  }];
});
})();

/******/ })()
;
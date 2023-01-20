..  include:: /Includes.rst.txt


======
Routes
======

With TYPO3 9 you have the possibility to configure RouteEnhancers

Example Configuration
=====================

..  code-block:: none

    routeEnhancers:
      Yellowpages2Plugin:
        type: Extbase
        extension: Yellowpages2
        plugin: Directory
        routes:
          -
            routePath: '/first-company-page'
            _controller: 'Company::list'
          -
            routePath: '/company-page-{page}'
            _controller: 'Company::list'
            _arguments:
              page: '@widget_0/currentPage'
          -
            routePath: '/company-by-letter/{letter}'
            _controller: 'Company::list'
          -
            routePath: '/show/{company_title}'
            _controller: 'Company::show'
            _arguments:
              company_title: company
        requirements:
          letter: '^(0-9|[a-z])$'
          company_title: '^[a-zA-Z0-9]+$'
        defaultController: 'Company::list'
        aspects:
          company_title:
            type: PersistedAliasMapper
            tableName: tx_yellowpages2_domain_model_company
            routeFieldName: path_segment

(function(angular, $, _) {

  angular.module('myemma').config(function($routeProvider) {
      $routeProvider.when('/my-emma-accounts', {
        controller: 'MyemmaMyEmmaAccounts',
        templateUrl: '~/myemma/MyEmmaAccounts.html',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
          accounts: function(crmApi) {
            return crmApi('MyEmmaAccount', 'get', {});
          }
        }
      });
    }
  );

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   myContact -- The current contact, defined above in config().
  angular.module('myemma').controller('MyemmaMyEmmaAccounts', function($scope, crmApi, crmStatus, crmUiHelp, accounts) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('myemma');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/myemma/MyEmmaAccounts'}); // See: templates/CRM/myemma/MyEmmaAccounts.hlp

    // We have myContact available in JS. We also want to reference it in HTML.
    $scope.accounts = accounts.values;

    $scope.save = function save() {
      return crmStatus(
        // Status messages. For defaults, just use "{}"
        //{start: ts('Saving...'), success: ts('Saved')},
        // The save action. Note that crmApi() returns a promise.
        /*crmApi('Contact', 'create', {
          id: myContact.id,
          first_name: myContact.first_name,
          last_name: myContact.last_name
        })*/
      );
    };
  });

})(angular, CRM.$, CRM._);

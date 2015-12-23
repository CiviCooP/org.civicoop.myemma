(function(angular, $, _) {

  angular.module('myemma').config(function($routeProvider) {
      $routeProvider.when('/my-emma-accounts', {
        controller: 'MyemmaMyEmmaAccounts',
        templateUrl: '~/myemma/MyEmmaAccounts.html',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
          accounts: function(crmApi) {
            return crmApi('MyEmmaAccount', 'get', {
              sequential: 1
            });
          },
          groups: function(crmApi) {
            return crmApi('Group', 'get', {
              sequential: 1,
              "options": {"limit":0}
            });
          }
        }
      });
      $routeProvider.when('/my-emma-accounts/:id', {
        controller: 'MyemmaMyEmmaAccount',
        templateUrl: '~/myemma/MyEmmaAccountEdit.html',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
          account: function($route, crmApi) {
            if ($route.current.params.id !== 'new') {
              return crmApi('MyEmmaAccount', 'getsingle', {
                id: $route.current.params.id
              });
            } else {
              return {
                name: '',
                public_key: '',
                private_key: '',
              };
            }
          },
          groups: function(crmApi) {
            return crmApi('Group', 'get', {
              sequential: 1,
              "options": {"limit":0}
            });
          }
        }
      });
    }
  );

  angular.module('myemma').controller('MyemmaMyEmmaAccount', function($scope, crmApi, crmStatus, crmUiHelp, account, groups) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('myemma');
    $scope.account = account;
    $scope.groups = groups.values;
    if (account.parent_group_id) {
      for(var i=0; i < groups.values.length; i++) {
        if (groups.values[i].id = account.parent_group_id) {
          $scope.group = groups.values[i];
        }
      }
    }

    $scope.save = function() {
      $scope.account.parent_group_id = $scope.group.id;
      var result = crmApi('MyEmmaAccount', 'create', $scope.account, true);
      result.then(function(data) {
        if (data.is_error === 0 || data.is_error == '0') {
          $scope.account.id = data.id;
          window.location.href = '#/my-emma-accounts';
        }
      });
    };
  });

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   myContact -- The current contact, defined above in config().
  angular.module('myemma').controller('MyemmaMyEmmaAccounts', function($scope, crmApi, crmStatus, crmUiHelp, accounts, groups) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('myemma');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/myemma/MyEmmaAccounts'}); // See: templates/CRM/myemma/MyEmmaAccounts.hlp

    // We have myContact available in JS. We also want to reference it in HTML.
    $scope.accounts =  accounts.values;
    $scope.groups = groups.values;

    $scope.deleteAccount = function deleteAccount(account) {
      var index = $scope.accounts.indexOf(account);
      crmApi('MyEmmaAccount', 'delete', {
        id: account.id
      }).then(function() {
        if (index >= 0) {
          $scope.accounts.splice(index, 1)
        }
      });
    };

    $scope.testconnection = function(account) {
      crmApi('MyEmmaAccount', 'testconnection', {
        id: account.id
      }, {
        error: function (data) {
          CRM.alert(data.error_message, ts('Conection failed'), 'error');
        },
        success: function(data) {
          CRM.alert(ts('Conection OK'), 'My Emma', 'success');
        }
      });
    };

    $scope.field_mappings = function(account) {
      window.location = CRM.url('civicrm/admin/my_emma_account/field_map', {'account_id' : account.id});
    }
  });

})(angular, CRM.$, CRM._);

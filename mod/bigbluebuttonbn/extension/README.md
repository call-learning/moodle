## BigBlueButtonBN extension subplugins

The plugins have for now at least two classes that should be implemented. To add new behaviour you can redefine any class located in
and place them in your plugin with exactly the same name but in a different namespace for example \\bbbext_<YOUREXTENSION>\\bigbluebuttonbn\\mod_instance_helper to
extend hooks from the mod_instance_helper class.
* action_url: inherit this class and redefine the execute method so add new parameter when we send an action url to the blindside server.
* mod_instance_helper : inherit this class so all methods will be called when we either add/delete/or update a module instance.

Some examples provided in the tests/fixtures/simple folder.

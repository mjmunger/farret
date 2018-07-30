# farret
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mjmunger/farret/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/farret/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mjmunger/farret/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/farret/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/mjmunger/farret/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/farret/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mjmunger/farret/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Find And Recrusively Replace Email Templates

## Installation

`composer require hphio/farret dev-master`

## Requirements

This was developed on, and intended for PHP v7.0+. It *may* work on lower versions, but YMMV.

## Documentation

### How to use this package.

This package revolves around a single class, the `Notif`, which represents a notification your system may send out. The Notif is designed to work with PHPMailer, but can be used by itself.

To create an email notification, simply create an html email with the desired look and feel, and insert template tags into the email where appropriate. Then, use code like this to create a fully rendered, well-formed email ready to be sent:

### Template syntax

There are three types of template tags in farret:

* `{{ TAGS }}`
* `{% HOOKS %}`
* `{% HOOKS|WITH|ARGUMENTS %}`

#### Tags

A `tag` is a simple placehold where a find and replace operation will substitute the needed information where the tag was added to the email. These substitutions are global, so, using `{{ FIRSTNAME }}` to add "Michael" as the first name of the recipient will happen EVERYWHERE in the template. 

The matching engine is rather flexible, and considers the single space between the opening and closing pair of brackets optional. So, having the space (or having them unbalanced) doesn't matter. It will still get replaced. 

Example of valid tags:
* `{{FIRSTNAME}}`
* `{{FIRSTNAME }}`
* `{{ FIRSTNAME}}`
* `{{ FIRSTNAME }}`


### Example #1: A Regular notification

**Create the notif, and tell it where your templates are:**
````
$Notif = new Notif();
$Notif->setTemplateDirectory('/path/to/your/templates');
$Notif->setTemplate('sometemplate');
```` 

**Tell the notif what template tags to look for, and what they will be replaced with:**

*Use the `addFart` method. ("add Find And Replace Template").*
```` 
$Notif->addFart('FIRSTNAME', $firstname);
$Notif->addFart('LASTNAME', $lastname);
$Notif->addFart('SOMEURL', $url);
```` 
**Render the email**
```
$Notif->render();
```
This reads the template, and perofrms all the necessary find and replace operations to fill out the template properly, and the resulting HTML for the email is now available in `$Notif->body`;

#### Hooks
A `hook` is a special kind of tag, which expects to perform an operation and substitute the result of that operation at that location in the template. The most common type of hook is the date hook, which is a built-in action in the `Notif` class. 

For example, let's say you wanted to have a dynamic copyright statement in your template. The hook `{% YEAR %}` will substitute the current year (2018 as of this writing) at that location in the template.

There are three "magic" hooks that are built in to the `Notif` class, which are:

1. `{% YEAR %}` 
1. `{% MONTH %}` 
1. `{% DAY %}`
 
#### Hooks with arguments

Internally, hooks use a callback function to do some operation on the provided data. The YEAR, MONTH, and DAY hooks above are actually convenience functions (fascades) to the DATE method of the Notif class, which provide a single argument to that method. (See source code).

So, suppose we want to add the current date in the format `Y-m-d`. We can do that by adding the following hook:

`{% DATE|Y-m-d %}`

When the `Notif` class sees this, it will parse the tag to see that the hook is `DATE` and a single argument `Y-m-d` should be passed to it.

#### Creating custom actions

Actions rely on callbacks within the class. Let's say you had a need to compute a hash of the current date and put it in the email. You can create this by adding a hook into your template like so:

`{% HASH %}`

Of course, the `Notif` class itself does not support this natively, so the solution is to simple extend the Notif class and add the capabilities:

```
class MyNotif extends Notif
{
    public function makeHash($args) {
        $now = new DateTime();
        return md5sum($now->format("Y-m-d"));
    }
}
```

Now, you would use your customized `MyNotif` class to create your notifications instead of the base `Notif` class. Note that you have to register the hooks with your notification in order for it to work:

```
$MyNotif = new MyNotif();
$MyNotif->render();
```

#### Creating more complex custom actions

In continuing with our example, let's say we needed to perform some operation on a record of data (create a hash of a recipients firstname + the current timestamp). We would extend the original Notif class as we did above, but now, we will use additional template fields as part of the arguments passed to the hook:

```
{% HASH|{{ FIRSTNAME}} %}
```

```
class MyNotif extends Notif
{
    public function makeHash($args) {
        $firstname = $args[0];
        return md5sum($firstname . microtime(true) );
    }
}
```

In this case, the hook is detected, and all the template tags would be resolved prior to executing the callback, which will produce the final hash.
# Standards among the project

## Code standards

Since we are in a group and we develop the code independently from each other, let's stick to these code standards to be aligned.

#### How to use $_SESSION

Right now, $_SESSION exists (so you have a session) wherever you are. 
The following fields are set **only** if you are ideally logged in, so if you are in one of your pages.
Currently, we have one homepage for each of the "roles". This page should set properly all of this parameters, until we have a working login.

###### General parameters

- $_SESSION['user'] -> contains the username of the currently logged in user.

- $_SESSION['role'] -> contains the role of the currently logged in user: {"parent","teacher","admin","principal"}

###### Parent parameters

Parameters set in chooseChild.php:

- $_SESSION['child'] = Fiscal code of the selected child

- $_SESSION['childName'] = Name of the selected child

- $_SESSION['childSurname'] = Surname of the selected child

- $_SESSION['class'] = Class of the selected child

###### Typical error
**DO NOT** save an object of type db\<role> into the $_SESSION variable. When you close the page, PHP will try to store the variable into a document, so it will try to serialize the object db\<role> => it will not work.

## Guidelines to avoid compatibility issues

### Common errors

1. Every time you perform a multiline echo you **MUST** use an identifier that starts with underscore ("_") AND the closing identifier MUST be aligned on the left.

Example:

<pre>
CORRECT
echo <<<_PASTACASSA
    ciao
    ciao
    ciao
_PASTACASSA;

WRONG 

echo <<<_WRONG
    ciao
    ciao
    ciao
    _WRONG;
</pre>

2.

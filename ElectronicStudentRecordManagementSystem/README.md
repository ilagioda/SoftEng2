# Guidelines to avoid compatibility issues

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
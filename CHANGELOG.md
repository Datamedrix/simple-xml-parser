# Change Log

All notable changes to this project will be documented in this file.

# 1.0.3 (2019-09-09)

### Fixes

* **parser:** Add "Merge CDATA as text nodes" option to the default option setting.

<a name="1.0.2"></a>
# 1.0.2 (2018-10-30)

### Fixes

* **parser:** update regex to remove comment tags correctly

<a name="1.0.1"></a>
# 1.0.1 (2018-10-24)

### Fixes

* **parser:** fix some major issues
    * remove all comments from the given xml content
    * use a deeper declaration of the xpath to remove empty nodes
    * add an dummy array element to the default '@attribute' key to prevent strange copy behaviours of the empty array

<a name="1.0.0"></a>
# 1.0.0 (2018-10-23)

### Features

* **\*:** add simple XML Parser
    - `(new Parser($myXMLContent))->toArray()`

<a name="0.0.0"></a>
# 0.0.0 (2018-10-23)

### Chore
* **\*:** initial commit


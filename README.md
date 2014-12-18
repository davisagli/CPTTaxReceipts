cpttaxreceipts
==============

CPT Tax Receipts extension for CiviCRM

This CiviCRM extension is based on the CDN Tax Receipts extension
(https://github.com/jake-mw/CDNTaxReceipts) but with some significant
changes:

1. Only the annual tax receipt feature has been retained.
1. Annual tax receipts can be generated multiple times;
   this extension does not keep track of which contributions have already
   been receipted.
2. The generated receipts include a table of all contributions for the year.


To set up the extension:
------------

1. Make sure your CiviCRM Extensions directory is set (Administer > System Settings > Directories).
2. Make sure your CiviCRM Extensions Resource URL is set (Administer > System Settings > Resource URLs).
3. Unpack the code
    - cd extensions directory
    - git clone https://github.com/davisagli/cpttaxreceipts.git org.cpt.cpttaxreceipts
4. Enable the extension at Administer > System Settings > Manage Extensions
5. Configure CPT Tax Receipts at Administer > CiviContribute > CPT Tax Receipts. (Take note of the dimensions for each of the image parameters. Correct sizing is important. You might need to try a few times to get it right.)
6. Review permissions: The extension has added a new permission called "CiviCRM CPT Tax Receipts: Issue Tax Receipts".

Now you should be able to use the module.


Operations
------------

**Annual Tax Receipts**

These are receipts that collect all outstanding contributions for the year into one receipt.

To issue Annual Tax Receipts, go to Search > Find Contacts (or Search > Advanced Search), run a search for contacts, select one or more contacts, and select "Issue Annual Tax Receipts" in the actions drop-down. Follow on-screen instructions from there.

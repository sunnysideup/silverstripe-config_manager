<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <% base_tag %>
  <title>$Title</title>

</head>

<body>
    <h2>
        Also see:
    </h2>
    <ul>
        <li><a href="$Link(location)">Location</a></li>
        <li><a href="$Link(package/vendor/packagename)">Package</a></li>
        <li><a href="$Link(model)">Model</a></li>
    </ul>
    <main class="tfs-holder loading">

        <% include TableFilterSortHeader %>

        <table class="tfs-table">
            <thead>
                <tr>
                    <th class="col-2">
                        <a href="#" class="sortable" data-sort-field="Vendor" data-sort-direction="asc" data-sort-type="string">Vendor</a>
                    </th>
                    <th class="col-3">
                        <a href="#" class="sortable" data-sort-field="Package" data-sort-direction="asc" data-sort-type="string">Package</a>
                    </th>
                    <th class="col-4">
                        <a href="#" class="sortable" data-sort-field="ShorterClassName" data-sort-direction="asc" data-sort-type="string">Shorter ClassName</a>
                    </th>
                    <th class="col-5">
                        <a href="#" class="sortable" data-sort-field="Property" data-sort-direction="asc" data-sort-type="string">Property</a>
                    </th>
                    <th class="col-7">
                        <a href="#" class="sortable" data-sort-field="Type" data-sort-direction="asc" data-sort-type="number">Type</a>
                    </th>
                    <th class="col-8" data-sort-field="HasValue" data-sort-direction="desc" data-sort-type="string">
                        Value
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="tfstr">
                    <td class="col-2">
                        <span data-filter="Vendor" class="dl">{{= it.Vendor}}</span>
                    </td>
                    <td class="col-3">
                        <span data-filter="Package" class="dl">{{= it.Package}}</span>
                    </td>
                    <td class="col-4">
                        <span data-filter="ShorterClassName" class="dl">{{= it.ShorterClassName}}</span>
                    </td>
                    <td class="col-5">
                        <span data-filter="Property" class="dl">{{= it.Property}}</span>
                    </td>
                    <td class="col-6">
                        <span data-filter="Type" class="dl">{{= it.Type}}</span>
                    </td>
                    <td class="col-8">

                        {{? it.HasValue == 'yes' }}
                        <a href="#" class="more">show details</a>
                        <div class="hidden">
                            <p><strong>Is Default:</strong> {{= it.IsDefault }}<p>
                            <h5>Value</h5>
                            {{= it.Value}}
                            {{? it.IsDefault !== 'no' }}
                            {{? it.HasDefault === 'yes' }}
                            <h5>Default</h5>
                            {{= it.Default }}
                            {{?}}
                            {{?}}
                        </div>
                        {{??}}
                        not value set
                        {{?}}
                        {{? it.IsDefault !== 'no' }}
                        {{? it.HasDefault === 'yes' }}
                        <h5>Default</h5>
                        {{= it.Default }}
                        {{?}}
                        {{?}}
                    </td>
                </tr>
            </tbody>
        </table>

        <% include TableFilterSortFooter %>

    </main>
    <p>
        This config manager is provided by Sunny Side Up. Please visit <a href="http://ssmods.com/#~(cfi~(Team~(~(vtm~'sunnysideup~ivl~'sunnysideup)))~csr~(sdi~'desc~sct~'Created_U))">ssmods.com</a> for more modules.</p>
    </p>
    <p>
        Feedback welcome.
    </p>
</body>
</html>

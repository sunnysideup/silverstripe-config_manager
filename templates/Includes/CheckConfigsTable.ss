<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <% base_tag %>
  <title>$Title</title>

</head>

<body>
    <main class="tfs-holder loading">

    <% include TableFilterSortHeader %>

    <table class="tfs-table">
        <thead>
            <tr>
                <th class="col-1">
                    <a href="#" class="sortable" data-sort-field="ClassName" data-sort-direction="asc" data-sort-type="string" data-sort-only="true">Class Name</a>
                </th>
                <th class="col-2">
                    <a href="#" class="sortable" data-sort-field="Vendor" data-sort-direction="asc" data-sort-type="string" data-sort-only="true">Vendor</a>
                </th>
                <th class="col-3">
                    <a href="#" class="sortable" data-sort-field="Package" data-sort-direction="asc" data-sort-type="string" data-sort-only="true">Package</a>
                </th>
                <th class="col-4">
                    <a href="#" class="sortable" data-sort-field="ShortClassName" data-sort-direction="asc" data-sort-type="string" data-sort-only="true">ShortClassName</a>
                </th>
                <th class="col-5">
                    <a href="#" class="sortable" data-sort-field="Property" data-sort-direction="asc" data-sort-type="string" data-sort-only="true">Property</a>
                </th>
                <th class="col-6">
                    <a href="#" class="sortable" data-sort-field="IsSet" data-sort-direction="asc" data-sort-type="number" data-sort-only="true">Set</a>
                </th>
                <th class="col-7">
                    <a href="#" class="sortable" data-sort-field="IsInherited" data-sort-direction="asc" data-sort-type="number" data-sort-only="true">Inherited</a>
                </th>
                <th class="col-8">
                    Value
                </th>
                <th class="col-9">
                    more ...
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="tfstr" id="{{= it.ID }}">
                <td class="col-1">
                    <span data-filter="ClassName" class="more">{{= it.ClassName}}</span>
                </td>
                <td class="col-2">
                    <span data-filter="Vendor" class="more">{{= it.Vendor}}</span>
                </td>
                <td class="col-3">
                    <span data-filter="Package" class="more">{{= it.Package}}</span>
                </td>
                <td class="col-4">
                    <span data-filter="ShortClassName" class="more">{{= it.ShortClassName}}</span>
                </td>
                <td class="col-5">
                    <span data-filter="Variable" class="more">{{= it.Property}}</span>
                </td>
                <td class="col-6">
                    <span data-filter="IsSet" class="more">{{= it.IsSet}}</span>
                </td>
                <td class="col-7">
                    <span data-filter="IsInherited" class="more">{{= it.IsInherited}}</span>
                </td>
                <td class="col-8">
                    <div class="hidden">
                    {{= it.Value}}
                    </div>
                </td>
                <td class="col-9">
                    <a href="#" class="more">more</a>
                </td>
            </tr>
        </tbody>
    </table>

    <% include TableFilterSortFooter %>

    </main>

</body>
</html>

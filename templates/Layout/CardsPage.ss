<section class="content">
    <div class="row">
        <div class="medium-8 small-centered columns">
            $Content
            <%-- Start Cards --%>
            <%-- Check if enabled and present--%>
            <% if $CardsEnabled && $Cards %>
                <%-- Loop the cards in the order provided in the CMS--%>
                <% loop $Cards.Sort('SortOrder') %>
                    <%-- Display card only if image is present, adjust accordingly--%>
                    <% if $Image %>
                        <%-- Check link type and apply link if set--%>
                        <div <% if $LinkType != 'None' %>data-link="$getDataLink"<% end_if %>>
                            <img src="$Image.FocusFill(350,450).URL"/>
                            <% if $Title %><h2>$Title</h2><% end_if %>
                            <% if $SubTitle %><h3>$SubTitle</h3><% end_if %>
                            <%-- Get proper content field based on type of link--%>
                            <% if $LinkType = 'None' %>
                                <% if $Content %>$Content<% end_if %>
                            <% else %>
                                <% if $SimpleContent %><p>$SimpleContent</p><% end_if %>
                            <% end_if %>
                        </div>
                    <% end_if %>
                <% end_loop %>
            <% end_if %>
            <%-- End Cards --%>
        </div>
    </div>
</section>
{*
Payment processor configuration form.
*}
{$suffix = $payment_id|default:0}


<div class="control-group">
    <label class="control-label cm-required">test_public_key:</label>
    <div class="controls">
        <input type="text"
               name="payment_data[processor_params][test_public_key]"
               id="test_public_key"
               value="{$processor_params.test_public_key}"
        />
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-required">test_secret_key:</label>
    <div class="controls">
        <input type="password"
               name="payment_data[processor_params][test_secret_key]"
               id="test_secret_key"
               value="{$processor_params.test_secret_key}"
               autocomplete="new-password"
        />
    </div>
</div>


<div class="control-group">
    <label class="control-label cm-required">live_public_key:</label>
    <div class="controls">
        <input type="text"
               name="payment_data[processor_params][live_public_key]"
               id="live_public_key"
               value="{$processor_params.live_public_key}"
        />
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-required">live_secret_key:</label>
    <div class="controls">
        <input type="password"
               name="payment_data[processor_params][live_secret_key]"
               id="live_secret_key"
               value="{$processor_params.live_secret_key}"
               autocomplete="new-password"
        />
    </div>
</div>


<div class="control-group">
    <label class="control-label" for="currency">currency:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]"
                id="currency"
        >
                <option value="NGN" {if $processor_params.currency === "NGN"} selected="selected" {/if}>NGN</option>
                <option value="GHS" {if $processor_params.currency === "GHS"} selected="selected" {/if}>GHS</option>
                <option value="KES" {if $processor_params.currency === "KES"} selected="selected" {/if}>KES</option>
                <option value="XOF" {if $processor_params.currency === "XOF"} selected="selected" {/if}>XOF</option>
                <option value="UGX" {if $processor_params.currency === "UGX"} selected="selected" {/if}>UGX</option>
                <option value="CFA" {if $processor_params.currency === "CFA"} selected="selected" {/if}>CFA</option>
                <option value="TZS" {if $processor_params.currency === "TZS"} selected="selected" {/if}>TZS</option>
                <option value="USD" {if $processor_params.currency === "USD"} selected="selected" {/if}>USD</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="country">country:</label>
    <div class="controls">
        <select name="payment_data[processor_params][country]"
                id="country"
        >
            <option value="NG" {if $processor_params.country === "NG"} selected="selected" {/if}>NIGERIA</option>
            <option value="GH" {if $processor_params.country === "GH"} selected="selected" {/if}>GHANA</option>
            <option value="KE" {if $processor_params.country === "KE"} selected="selected" {/if}>KENYA</option>
            <option value="SN" {if $processor_params.country === "SN"} selected="selected" {/if}>SENEGAL</option>
            <option value="UG" {if $processor_params.country === "UG"} selected="selected" {/if}>UGANDA</option>
            <option value="TZ" {if $processor_params.country === "TZ"} selected="selected" {/if}>TANZANIA</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">Test/Live mode:</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]"
                id="mode"
        >
                <option value="test" {if $processor_params.mode === "test"} selected="selected" {/if}>Test</option>
                <option value="live" {if $processor_params.mode === "live"} selected="selected" {/if}>Live</option>
        </select>
    </div>
</div>

<div class="tab-pane fade " id="superAccess" role="tabpanel">
    <h3>Check Number API</h3>
    <p>Method : <code class="text-success">POST</code></p>
    <p>Endpoint: <code>{{ env('APP_URL') }}/super-access</code></p>

    <p>Request Body : (JSON) </p>
    <table class="table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>api_key</td>
                <td>string</td>
                <td>Yes</td>
                <td>API Key</td>
            </tr>
			<tr>
                <td>sender</td>
                <td>string</td>
                <td>Yes</td>
                <td>your number ex 62888xxxx</td>
            </tr>
            <tr>
                <td>method</td>
                <td>string</td>
                <td>Yes</td>
                <td>any valid makeWASocket method from <a href='https://baileys.whiskeysockets.io/functions/makeWASocket.html' target="_new">available methods</a>.</td>
            </tr>
            <tr>
                <td>params</td>
                <td>json</td>
                <td>only if required by method</td>
                <td>all required parameters by the method used.</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>Example (Get Profile Picture Url)  JSON Request</p>
    <pre class="bg-dark text-white">
      <code>
    {
      	"api_key":"1234567890",
    	"sender":"62888xxxx",
    	"method":"profilePictureUrl",
    	"params":["62888xxxx@c.us"]
    }
      </code>
    </pre>
    <p>Example (Update Profile Status)  JSON Request</p>
    <pre class="bg-dark text-white">
      <code>
    {
      	"api_key":"1234567890",
    	"sender":"62888xxxx",
    	"method":"updateProfileStatus",
    	"params":["Available"]
    }
      </code>
    </pre>
    <p>Example (Send Message)  JSON Request</p>
    <pre class="bg-dark text-white">
      <code>
    {
      	"api_key":"1234567890",
    	"sender":"62888xxxx",
    	"method":"sendMessage",
    	"params":["62888xxxx@c.us",{"text":"Hello from Super User"}]
    }
      </code>
    </pre>
</div>

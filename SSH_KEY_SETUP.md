# SSH Key Setup for faucetlist Account

## Problem

Previous attempts to add SSH key failed because the key was pasted in the wrong section.

## Solution

The RSA key is already generated and ready to use:

**Key file:** `~/.ssh/faucetlist_key_rsa`

**Public key content to paste:**

```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDShAbgUugz2df0XgIzRvj3JEtUKSKpzFz5J7WegYsUFsY/s46vs2N8r0ZpSy4u7pX5oWiIKG2ttjXP6khM25Od4cX1WOpMeV2EPdeVqojiVS6ghcQ9DUN4XbC03w98FSLOGAQMOs3SCh8DGMxTQcqb8L8CSPGwYLoR6z4YhZm6GRx8d7HRCPaxbV6XR3ZXR2MWmLRJ9JaE36jYL1IXWDHmvfhzUwkgyT0WQw63jOjhhv4NysCUKDeVGpJNvhjmpENqzkkvwAMxECAu4NcU4Tez5L0bZu0TwVt87GWAAFmG9MEyxGZiVtH93SzNXbTZWrNpTGEB2iOB9JlkDJ0q3wbkIKKTSTbtGapI7pZ6gfN/H6iQyDZNZAxa7SHP0z19lbqVWD/FEdt95XMXmVUDHtR5hfeMgsrMy9PNGWHMSZ8Vhl6EGmR+u9iH/QddM0i95/xj8HnxpFOG/aCaSTAMWSC6v+mwWx//bskj6OQ/1dYiUMCCDWS+3ZJyaQVRbIE9ESeCy5+RYNiPkgVy75I5SpMyO4opr1xHx9yDbqv6NHd76hvcSQgK3mXMBNFQygVSIdwOoLRUQKI+A+LATeEXZ5QAo3dyFsG0ENvz9OlUCPEHUaUzKpfuvtJbIm7jpogE1hG9/QJ2lw+XrEp/5pD8mcC28sGKSTR8sdka2IH+qVp4OQ==
```

## Steps to Add Key in DirectAdmin

1. Log into DirectAdmin: https://directadmin-de.kxe.io:2222/evo/login
   - User: `faucetlist`
   - Password: `Relation-229934-Intricate`

2. Navigate to **Account Manager** or **Account Settings**

3. Look for **SSH/Shell Access** or **SSH Keys** section

4. Find the **SSH Public Keys** field (NOT "Authorized Keys")

5. Paste the entire public key content above (the one starting with `ssh-rsa`)

6. Click **Save** or **Add Key**

7. Test the connection from local machine:
   ```bash
   ssh -i ~/.ssh/faucetlist_key_rsa faucetlist@directadmin-de.kxe.io -p 10500
   ```

8. If successful, you should get a shell prompt. Type `exit` to disconnect.

9. Then try the deploy script:
   ```bash
   ./deploy-to-directadmin.sh
   ```

## Troubleshooting

**Still getting "Permission denied":**
- Verify you pasted the key in the **SSH Public Keys** section
- Make sure the entire key is copied (starts with `ssh-rsa` and ends with `==`)
- Wait a few minutes for DirectAdmin to process the change
- Try SSH connection again

**Connection times out:**
- Check that SSH is enabled for the faucetlist account
- Verify the hosting plan includes SSH access
- Check firewall rules on DirectAdmin

**Key not accepted:**
- Ensure no extra spaces or line breaks were added when pasting
- Verify the key content matches exactly what's above
- Try regenerating a fresh key if needed

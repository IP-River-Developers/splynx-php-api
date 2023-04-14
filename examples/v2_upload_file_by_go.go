// Splynx API v2.0 demo script
// Author: Roman Muzichuk (Splynx s.r.o.)
// https://splynx.docs.apiary.io - API documentation

package main

import "time"
import "strconv"
import "crypto/hmac"
import "crypto/sha256"
import "io"
import "fmt"
import "net/url"
import "net/http"
import "os"
import "strings"
import "mime/multipart"
import "bytes"
import "path/filepath"

var (
    filePaths = []string{
        "./path/file1.txt",
        "./path/file2.png",
    }
    domainName = "YOUR_SPLYNX_DOMAIN"
    apiKey     = "YOUR_API_KEY"
    apiSecret  = "YOUR_API_SECRET"
    messageID  = "TICKET_MESSAGE_ID"
    nonce      int64
)

func main() {
    url := fmt.Sprintf("%s/api/2.0/admin/support/ticket-attachments?message_id=%d", domainName, messageID)

    request := makeRequest(url, filePaths)

    client := http.Client{}
    response, _ := client.Do(request)

    fmt.Println("Response status:", response.Status)
    body := &bytes.Buffer{}
    _, err := body.ReadFrom(response.Body)
    if err != nil {
        fmt.Println(err.Error())
    }
    response.Body.Close()
    fmt.Println(body)
}

func generateSignature() string {
    nonce = int64(time.Duration(time.Now().UnixNano()) / time.Millisecond)
    hashValue := strconv.FormatInt(nonce, 10) + apiKey
    hash := hmac.New(sha256.New, []byte(apiSecret))
    io.WriteString(hash, hashValue)
    return strings.ToUpper(fmt.Sprintf("%x", hash.Sum(nil)))
}

func getAuthHeaders() http.Header {
    authData := url.Values{}
    authData.Set("key", apiKey)
    authData.Set("signature", generateSignature())
    authData.Set("nonce", strconv.FormatInt(nonce, 10))

    headers := http.Header{}
    headers.Set("Authorization", "Splynx-EA ("+authData.Encode()+")")
    return headers
}

func makeRequest(url string, files []string) *http.Request {
    body := &bytes.Buffer{}
    writer := multipart.NewWriter(body)

    filesIo := make(map[string]io.Reader)
    for i, filePath := range files {
        file, err := os.Open(filePath)
        defer file.Close()
        if err != nil {
            continue
        }

        filesIo[fmt.Sprintf("files[%d]", i)] = file
        part, err := writer.CreateFormFile(fmt.Sprintf("files[%d]", i), filepath.Base(filePath))
        if err != nil {
            fmt.Println(err.Error())
        }
        _, err = io.Copy(part, file)
        if err != nil {
            fmt.Println(err.Error())
        }
    }
    writer.Close()

    request, _ := http.NewRequest("POST", url, body)
    request.Header = getAuthHeaders()
    request.Header.Add("Content-Type", writer.FormDataContentType())

    return request
}